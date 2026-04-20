# Naar School in Vlaanderen - Project Documentation

## Table of Contents
1. [Project Overview](#project-overview)
2. [Technology Stack](#technology-stack)
3. [Project Architecture](#project-architecture)
4. [User Creation Flow & API Integration](#user-creation-flow--api-integration)
5. [Directory Structure](#directory-structure)
6. [Configuration](#configuration)
7. [Key Components](#key-components)
8. [Authentication & Authorization](#authentication--authorization)
9. [Database Schema](#database-schema)
10. [Deployment](#deployment)

---

## Project Overview

**Naar School in Vlaanderen** is a Symfony 5.4-based web application designed to manage schools, users, and educational data in Flanders, Belgium. The application integrates with Auth0 for user management and authentication, and with the Onderwijs Vlaanderen (OWV) API for educational data.

### Main Features
- School management (CRUD operations)
- User management with role-based access control
- School year management
- Educational programs and grades tracking
- Audit logging for changes
- Export functionality for school data
- API integration with Auth0 and OWV

---

## Technology Stack

### Backend
- **PHP**: >= 7.4
- **Symfony**: 5.4.*
- **Doctrine ORM**: ^2.8 (Database abstraction)
- **EasyAdmin Bundle**: 3.2.* (Admin interface)
- **MySQL**: 5.7 (Database)

### Frontend
- **Twig**: Template engine
- **Webpack Encore**: Asset management
- **JavaScript**: For interactive features

### Authentication & Authorization
- **Auth0**: Third-party authentication service
- **HWI OAuth Bundle**: ^1.3 (OAuth integration)

### API & HTTP
- **Guzzle HTTP Client**: API communication
- **FOSRestBundle**: ^3.5 (REST API)
- **Nelmio API Doc**: ^4.11 (API documentation)

### Additional Libraries
- **Gedmo Doctrine Extensions**: ^3.1 (Soft delete, timestamps)
- **BabDev Pagerfanta**: ^2.9 (Pagination)
- **League CSV**: ^9.6 (CSV export/import)
- **Symfony Messenger**: Asynchronous task handling

---

## Project Architecture

### MVC Pattern
The application follows the Model-View-Controller (MVC) pattern:
- **Models**: Entity classes in `src/Entity/` and domain models in `src/Model/`
- **Views**: Twig templates in `templates/`
- **Controllers**: Controller classes in `src/Controller/`

### Service Layer
Business logic is encapsulated in services located in `src/Service/`:
- Auth0 integration services
- Data transformation services
- API communication services
- Export/Import services

### Event-Driven Architecture
The application uses Symfony's event dispatcher for:
- Audit logging
- Automatic data population
- User synchronization with Auth0
- Search indexing

---

## User Creation Flow & API Integration

### Overview
When a user is created through the admin interface, the application posts to the **Auth0 Management API** to create the user account externally.

### Detailed Flow

#### 1. User Creation Request
**File**: `src/Controller/Admin/UserController.php`

```php
public function create(Request $request, UserRepository $userRepository, UserTransformer $transformer, AdminUrlGenerator $adminUrlGenerator): Response
```

**Process**:
1. User fills out the form (`UserType` form)
2. Form is validated
3. User data is transformed to `CreateUserRequest`
4. `UserRepository::createUser()` is called
5. Password reset email is sent via `UserRepository::resetPassword()`

#### 2. Repository Layer
**File**: `src/Service/Auth0/UserRepository.php`

```php
public function createUser(CreateUserRequest $userRequest): void
{
    try {
        $this->client->post('/api/v2/users', $userRequest);
    } catch (ClientException $clientException) {
        if (Response::HTTP_CONFLICT === $clientException->getCode()) {
            throw new UserExistsException();
        }
        throw $clientException;
    }
}
```

**Purpose**: Handles the business logic and error handling for user creation.

#### 3. Auth0 Client
**File**: `src/Service/Auth0/Client.php`

```php
public function post(string $uri, ?RequestInterface $model, array $options = [], bool $requiresClientId = false): void
```

**Process**:
1. Retrieves OAuth access token from Auth0
2. Serializes the request data to JSON
3. Makes HTTP POST request to Auth0 API
4. Handles authentication headers

#### 4. HTTP Client Configuration
**File**: `config/services.yaml`

```yaml
App\Service\Client\Auth0ClientInterface:
    class: GuzzleHttp\Client
    arguments:
        - base_uri: '%env(resolve:AUTH0_DOMAIN)%'
```

**Purpose**: Configures the Guzzle HTTP client with the Auth0 base URL.

### API Configuration Location

The API endpoint configuration is found in **TWO places**:

#### A. Base URL Configuration
**File**: `config/services.yaml` (lines 47-49)
```yaml
App\Service\Client\Auth0ClientInterface:
    class: GuzzleHttp\Client
    arguments:
        - base_uri: '%env(resolve:AUTH0_DOMAIN)%'
```

#### B. Environment Variables
**File**: `.env` (lines 34-41)
```dotenv
###> Auth0 Authentication ###
AUTH0_DOMAIN=https://dev-xywioeq5.eu.auth0.com
AUTH0_CLIENT_ID=UzSFfg3Mf25tg6RlAvggkmZ6Uy4e08uh
AUTH0_CLIENT_SECRET=eTVi7t9aoaETcBMbFUh4-utcIXXRfmz_eNsdWwA9NkLw5D0nMG6Omolj7GKQ6XV7

AUTH0_MANAGEMENT_CLIENT_ID=NK0VZtwNE1CX7uAyNs33EpwjbJcrsXLO
AUTH0_MANAGEMENT_CLIENT_SECRET=jF8Vc2VaBejy8kD_hJke3UKQAeUprdVHWIMwer8Xj6WgzcOq_HBME8WJm9VKF_zQ
AUTH0_MANAGEMENT_AUDIENCE=https://dev-xywioeq5.eu.auth0.com/api/v2/
###< Auth0 Authentication ###
```

### API Endpoints Used

#### User Creation
- **Endpoint**: `POST /api/v2/users`
- **Base URL**: `https://dev-xywioeq5.eu.auth0.com`
- **Full URL**: `https://dev-xywioeq5.eu.auth0.com/api/v2/users`
- **Purpose**: Creates a new user in Auth0

#### Password Reset
- **Endpoint**: `POST /dbconnections/change_password`
- **Base URL**: `https://dev-xywioeq5.eu.auth0.com`
- **Full URL**: `https://dev-xywioeq5.eu.auth0.com/dbconnections/change_password`
- **Purpose**: Triggers password reset email

#### OAuth Token
- **Endpoint**: `POST /oauth/token`
- **Base URL**: `https://dev-xywioeq5.eu.auth0.com`
- **Full URL**: `https://dev-xywioeq5.eu.auth0.com/oauth/token`
- **Purpose**: Obtains access token for Management API

### Request/Response Models

#### CreateUserRequest
**File**: `src/Model/Request/CreateUserRequest.php`

**Fields**:
- `email`: User's email address
- `givenName`: First name
- `familyName`: Last name
- `connection`: Auth0 connection (default: "Naarschoolin")
- `password`: User's password
- `userMetadata`: Array containing:
  - `school`: Array of school IDs
  - `editusers`: Permission flag
  - `editvestigingen`: Permission flag
  - `superadmin`: Admin flag

#### UserResponse
**File**: `src/Model/Response/UserResponse.php`

Used to deserialize API responses from Auth0.

---

## Directory Structure

```
naarschoolinvlaanderen-master/
├── assets/                          # Frontend assets
│   ├── admin.js                     # Admin panel JS
│   ├── app.js                       # Main application JS
│   ├── base.js                      # Base JS functionality
│   ├── bootstrap.js                 # Bootstrap initialization
│   ├── fixtures/                    # Test data fixtures
│   ├── images/                      # Image assets
│   └── styles/                      # CSS/SCSS files
│
├── bin/                             # Executable scripts
│   ├── console                      # Symfony console
│   └── phpunit                      # PHPUnit test runner
│
├── config/                          # Configuration files
│   ├── bundles.php                  # Bundle registration
│   ├── services.yaml                # Service configuration
│   ├── routes.yaml                  # Route definitions
│   ├── packages/                    # Package-specific configs
│   │   ├── doctrine.yaml            # Database config
│   │   ├── security.yaml            # Security & authentication
│   │   ├── hwi_oauth.yaml           # OAuth configuration
│   │   └── ...
│   └── routes/                      # Route definitions by area
│
├── migrations/                      # Database migrations
│   └── Version*.php                 # Migration files
│
├── public/                          # Web root directory
│   ├── index.php                    # Application entry point
│   ├── build/                       # Compiled assets
│   └── bundles/                     # Public bundle assets
│
├── src/                             # Application source code
│   ├── Kernel.php                   # Application kernel
│   │
│   ├── Controller/                  # Controllers
│   │   ├── Admin/                   # Admin controllers
│   │   │   ├── UserController.php           # User management
│   │   │   ├── SchoolCrudController.php     # School CRUD
│   │   │   ├── AuditLogCrudController.php   # Audit logs
│   │   │   └── DashboardController.php      # Admin dashboard
│   │   ├── Api/                     # API controllers
│   │   ├── IndexController.php      # Homepage controller
│   │   └── DisclaimerPagesController.php
│   │
│   ├── Entity/                      # Doctrine entities (database models)
│   │   ├── School.php               # School entity
│   │   ├── SchoolYear.php           # School year entity
│   │   ├── SchoolEducation.php      # Education programs
│   │   ├── AuditLog.php             # Audit log entries
│   │   ├── User/                    # User-related entities
│   │   └── Webhook/                 # Webhook entities
│   │
│   ├── Model/                       # Domain models & DTOs
│   │   ├── User.php                 # User domain model
│   │   ├── Role.php                 # Role definitions
│   │   ├── AuditLogFields.php       # Audit field definitions
│   │   ├── Request/                 # API request models
│   │   │   ├── CreateUserRequest.php
│   │   │   └── UpdateUserRequest.php
│   │   ├── Response/                # API response models
│   │   │   └── UserResponse.php
│   │   └── ...
│   │
│   ├── Repository/                  # Doctrine repositories
│   │   ├── SchoolRepository.php
│   │   ├── SchoolYearRepository.php
│   │   └── AuditLogRepository.php
│   │
│   ├── Service/                     # Business logic services
│   │   ├── Auth0/                   # Auth0 integration
│   │   │   ├── Client.php           # Auth0 HTTP client
│   │   │   └── UserRepository.php   # Auth0 user operations
│   │   ├── Provider/                # Data providers
│   │   │   └── UserProvider.php
│   │   ├── Transformer/             # Data transformers
│   │   │   └── UserTransformer.php
│   │   ├── Educations/              # Education services
│   │   ├── Exports/                 # Export services
│   │   ├── Filter/                  # Filter services
│   │   └── Webhook/                 # Webhook handlers
│   │
│   ├── Form/                        # Form types
│   │   ├── User/                    # User forms
│   │   │   └── UserType.php
│   │   └── School/                  # School forms
│   │
│   ├── EventSubscriber/             # Event subscribers
│   │   ├── UserUpdatedEventSubscriber.php
│   │   ├── SchoolEducationChangeLogEventSubscriber.php
│   │   ├── LogoutEventSubscriber.php
│   │   └── Log/                     # Logging subscribers
│   │
│   ├── Security/                    # Security components
│   │   ├── OAuthUserProvider.php
│   │   ├── Auth0ResourceOwner.php
│   │   └── Api/                     # API authentication
│   │
│   ├── Command/                     # Console commands
│   ├── DataFixtures/                # Test data fixtures
│   ├── Event/                       # Custom events
│   ├── Exception/                   # Custom exceptions
│   ├── Message/                     # Messenger messages
│   └── MessageHandler/              # Message handlers
│
├── templates/                       # Twig templates
│   ├── base.html.twig               # Base template
│   ├── index.html.twig              # Homepage
│   ├── Admin/                       # Admin templates
│   │   ├── AuditLog/
│   │   └── ...
│   ├── Users/                       # User templates
│   │   ├── index.html.twig          # User list
│   │   └── create.html.twig         # User create/edit
│   ├── Export/                      # Export templates
│   ├── Disclaimers/                 # Disclaimer pages
│   ├── Macro/                       # Twig macros
│   └── includes/                    # Reusable template parts
│
├── tests/                           # Test files
│   └── bootstrap.php                # Test bootstrap
│
├── translations/                    # Translation files
│   └── messages.nl.yaml             # Dutch translations
│
├── var/                             # Variable files (not in git)
│   ├── cache/                       # Application cache
│   └── log/                         # Application logs
│
├── vendor/                          # Composer dependencies
│
├── composer.json                    # PHP dependencies
├── package.json                     # Node.js dependencies
├── symfony.lock                     # Symfony recipes lock
├── webpack.config.js                # Webpack configuration
├── docker-compose.yml               # Docker setup
├── RoboFile.php                     # Robo task runner
└── README.md                        # Project readme
```

---

## Configuration

### Environment Variables

All sensitive configuration is stored in environment variables (`.env` file):

#### Application
- `APP_ENV`: Application environment (dev/staging/prod)
- `APP_SECRET`: Secret key for CSRF protection and encryption

#### Database
- `DATABASE_URL`: MySQL connection string

#### Auth0 (User Authentication)
- `AUTH0_DOMAIN`: Auth0 tenant domain
- `AUTH0_CLIENT_ID`: OAuth application client ID
- `AUTH0_CLIENT_SECRET`: OAuth application secret

#### Auth0 Management API (User Management)
- `AUTH0_MANAGEMENT_CLIENT_ID`: Management API client ID
- `AUTH0_MANAGEMENT_CLIENT_SECRET`: Management API secret
- `AUTH0_MANAGEMENT_AUDIENCE`: Management API audience URL

#### External APIs
- `OWV_API_KEY`: Onderwijs Vlaanderen API key
- `OWV_URL`: Onderwijs Vlaanderen API base URL

#### Messaging
- `MESSENGER_TRANSPORT_DSN`: Message queue transport (doctrine://default)

### Service Configuration

**File**: `config/services.yaml`

Key service configurations:
- Auto-wiring enabled for all services in `src/`
- Auth0 Client configured with environment variables
- Education API services configured
- Event subscribers registered
- Gedmo soft-delete functionality enabled

---

## Key Components

### 1. Controllers

#### UserController
**Location**: `src/Controller/Admin/UserController.php`

**Routes**:
- `GET /admin/users` - List users with pagination
- `GET /admin/users/create` - Show user creation form
- `POST /admin/users/create` - Create new user
- `GET /admin/users/edit` - Show user edit form
- `POST /admin/users/edit` - Update user
- `GET /admin/users/delete/{id}` - Delete user

**Key Methods**:
- `index()`: Lists users with search and pagination
- `create()`: Creates new user in Auth0 and sends password reset
- `edit()`: Updates user in Auth0
- `delete()`: Soft deletes user from Auth0

#### SchoolCrudController
**Location**: `src/Controller/Admin/SchoolCrudController.php`

Manages school CRUD operations using EasyAdmin.

#### AuditLogCrudController
**Location**: `src/Controller/Admin/AuditLogCrudController.php`

Displays audit logs with filtering capabilities.

### 2. Services

#### Auth0 Client
**Location**: `src/Service/Auth0/Client.php`

**Purpose**: HTTP client wrapper for Auth0 API communication.

**Key Methods**:
- `request()`: Generic API request
- `post()`: POST request with authentication
- `patch()`: PATCH request for updates
- `delete()`: DELETE request
- `getAccessToken()`: Retrieves OAuth access token

#### UserRepository
**Location**: `src/Service/Auth0/UserRepository.php`

**Purpose**: Repository pattern for Auth0 user operations.

**Key Methods**:
- `findById()`: Retrieves user by ID (with caching)
- `findAll()`: Lists all users
- `findByQuery()`: Searches users
- `createUser()`: Creates new user
- `updateUser()`: Updates existing user
- `resetPassword()`: Triggers password reset
- `deleteUser()`: Deletes user

#### UserTransformer
**Location**: `src/Service/Transformer/UserTransformer.php`

**Purpose**: Transforms data between different representations.

**Transformations**:
- Domain model ↔ Auth0 API request
- Auth0 API response ↔ Domain model
- Form data ↔ Domain model

### 3. Forms

#### UserType
**Location**: `src/Form/User/UserType.php`

**Purpose**: Form builder for user creation and editing.

**Fields**:
- Email
- First name (given name)
- Last name (family name)
- Schools (multiple selection)
- Permissions (checkboxes)

### 4. Entities

#### School
**Location**: `src/Entity/School.php`

**Purpose**: Represents a school in the database.

**Key Fields**:
- ID, name, address, postal code, city
- Contact info (email, phone, website)
- Geographic data (region)
- Relationships with school years and educations
- Soft-deleteable (using Gedmo)

#### SchoolYear
**Location**: `src/Entity/SchoolYear.php`

**Purpose**: Represents an academic year.

**Key Fields**:
- Start year, end year
- School relationship
- Education programs for that year

#### AuditLog
**Location**: `src/Entity/AuditLog.php`

**Purpose**: Tracks changes made to entities.

**Key Fields**:
- User ID, email, name
- School and school year
- Field changed
- Old value and new value
- Timestamp

### 5. Event Subscribers

#### UserUpdatedEventSubscriber
**Location**: `src/EventSubscriber/UserUpdatedEventSubscriber.php`

**Purpose**: Handles post-user-update actions.

**Actions**:
- Cache invalidation
- Audit logging
- Synchronization tasks

#### SchoolEducationChangeLogEventSubscriber
**Location**: `src/EventSubscriber/SchoolEducationChangeLogEventSubscriber.php`

**Purpose**: Automatically logs changes to school educations.

---

## Authentication & Authorization

### Authentication Flow

1. **User Login**: Users are redirected to Auth0 for authentication
2. **OAuth Callback**: Auth0 redirects back with authorization code
3. **Token Exchange**: Application exchanges code for access token
4. **User Data**: Application retrieves user profile from Auth0
5. **Session Creation**: Local session is created with user data

### OAuth Configuration
**File**: `config/packages/hwi_oauth.yaml`

```yaml
hwi_oauth:
    firewall_names: [main]
    resource_owners:
        auth0:
            type: oauth2
            class: 'App\Security\Auth0ResourceOwner'
            client_id: "%env(AUTH0_CLIENT_ID)%"
            client_secret: "%env(AUTH0_CLIENT_SECRET)%"
            base_url: "%env(AUTH0_DOMAIN)%"
            scope: "openid profile email roles schools"
```

### Role Hierarchy
**File**: `config/packages/security.yaml`

```
ROLE_SUPER_ADMIN
├── ROLE_GROUP_ADMIN
├── ROLE_SCHOOL_ADMIN
└── ROLE_SCHOOL_YEAR_ADMIN

ROLE_GROUP_ADMIN
├── ROLE_USER_ADMIN
├── ROLE_ADD_SCHOOL
├── ROLE_EDIT_SCHOOL_EDUCATIONS
├── ROLE_EDIT_SCHOOL
├── ROLE_DELETE_SCHOOL
└── ROLE_CREATE_USER

ROLE_SCHOOL_ADMIN
├── ROLE_USER_ADMIN
├── ROLE_EDIT_SCHOOL
├── ROLE_EDIT_SCHOOL_EDUCATIONS
├── ROLE_EDIT_SCHOOL_EDUCATION_NUMBERS
├── ROLE_IMPORT_SCHOOL_EDUCATIONS
└── ROLE_EXPORT_SCHOOL_EDUCATION_NUMBERS

ROLE_SCHOOL_YEAR_ADMIN
├── ROLE_EDIT_SCHOOL_YEAR
└── ROLE_ADD_SCHOOL_YEAR

ROLE_USER_ADMIN
├── ROLE_DELETE_USER
└── ROLE_EDIT_USER
```

### Permission System

**Location**: `src/Model/Role.php`

Roles are stored in Auth0 user metadata and synchronized with the local application. Access control is enforced using Symfony's security voters and the `@Security` annotation on controllers.

---

## Database Schema

### Main Tables

1. **app_schools**: Stores school information
2. **app_school_years**: Academic years
3. **app_school_educations**: Educational programs offered
4. **app_school_education_underrepresented_groups**: Diversity tracking
5. **app_audit_logs**: Change tracking
6. **app_api_users**: API access tokens
7. **app_webhooks**: Webhook configurations

### Relationships

- School 1:N SchoolYear
- SchoolYear 1:N SchoolEducation
- SchoolEducation 1:N SchoolEducationUnderrepresentedGroup
- School N:M SchoolType
- School N:M SchoolLevel

### Soft Deletes

Most entities use Gedmo's soft delete functionality, meaning records are marked as deleted but not physically removed from the database.

---

## Deployment

### Automatic Setup
```bash
symfony php vendor/bin/robo run:project
```

### Manual Setup
```bash
# Start Docker containers
docker-compose up -d

# Install dependencies
symfony composer install

# Reset database
symfony composer reset

# Start Symfony proxy
symfony proxy:start

# Attach domain
symfony proxy:domain:attach naarschoolinvlaanderen.be

# Start development server
symfony serve -d
```

### Production Deployment
```bash
symfony php ./vendor/bin/dep deploy production
```

### Database Migrations
```bash
# Create migration
symfony console make:migration

# Run migrations
symfony console doctrine:migrations:migrate

# Rollback migration
symfony console doctrine:migrations:execute --down VERSION
```

### Cache Management
```bash
# Clear cache
symfony console cache:clear

# Warm up cache
symfony console cache:warmup
```

---

## API Documentation

### Internal API
The application exposes a REST API documented using Nelmio API Doc.

**Access**: `/api/doc`

### External APIs

#### Auth0 Management API v2
- **Base URL**: `https://dev-xywioeq5.eu.auth0.com/api/v2/`
- **Authentication**: OAuth 2.0 Client Credentials
- **Documentation**: https://auth0.com/docs/api/management/v2

**Endpoints Used**:
- `POST /api/v2/users` - Create user
- `GET /api/v2/users` - List users
- `GET /api/v2/users/{id}` - Get user
- `PATCH /api/v2/users/{id}` - Update user
- `DELETE /api/v2/users/{id}` - Delete user

#### Onderwijs Vlaanderen API
- **Purpose**: Retrieve official education data
- **Authentication**: API Key
- **Base URL**: Configured in `OWV_URL` environment variable

---

## Development Workflow

### Code Quality Tools

#### PHPStan (Static Analysis)
```bash
vendor/bin/phpstan analyse
```

Configuration: `phpstan.neon`

#### PHP CS Fixer (Code Style)
```bash
vendor/bin/php-cs-fixer fix
```

#### Psalm (Static Analysis)
```bash
vendor/bin/psalm
```

Configuration: `psalm.xml`

### Testing
```bash
# Run all tests
symfony php bin/phpunit

# Run specific test
symfony php bin/phpunit tests/SomeTest.php
```

### Frontend Build
```bash
# Install dependencies
npm install

# Build for development
npm run dev

# Build for production
npm run build

# Watch mode
npm run watch
```

---

## Troubleshooting

### Common Issues

#### Issue: Auth0 API returns 401 Unauthorized
**Solution**: Check that Auth0 Management API credentials are correct in `.env` and that the API has necessary permissions.

#### Issue: Database connection fails
**Solution**: Verify `DATABASE_URL` in `.env` and ensure database server is running.

#### Issue: Assets not loading
**Solution**: Run `npm run build` to compile frontend assets.

#### Issue: Cache issues after deployment
**Solution**: Clear cache with `symfony console cache:clear --env=prod`.

---

## Security Considerations

1. **Environment Variables**: Never commit `.env` file with real credentials
2. **API Keys**: Rotate API keys regularly
3. **Auth0 Tokens**: Access tokens are cached for 1 hour
4. **HTTPS**: Always use HTTPS in production
5. **CSRF Protection**: Enabled by default in Symfony forms
6. **SQL Injection**: Protected by Doctrine ORM parameter binding
7. **XSS**: Twig auto-escapes output by default

---

## Maintenance

### Regular Tasks

1. **Database Backups**: Automated via cron (see `etc/crontab`)
2. **Log Rotation**: Configured in Monolog
3. **Cache Warming**: After each deployment
4. **Security Updates**: Regularly update dependencies

### Monitoring

- Application logs: `var/log/`
- Web server logs: Check Docker logs
- Auth0 logs: Available in Auth0 dashboard

---

## Contact & Support

For questions or issues related to this project, consult:
- Project documentation
- Symfony documentation: https://symfony.com/doc
- Auth0 documentation: https://auth0.com/docs

---

*Last Updated: November 2025*
*Project Version: Symfony 5.4*

