# Naar school in vlaanderen

## Setup

### Automatic setup
This will run the steps defined in the Manual Setup, except for the resetdb step.
```
symfony php vendor/bin/robo run:project
```

### Reset database
```
symfony composer reset
```

### Manual setup
```
docker-compose up -d
symfony composer install
symfony composer reset
symfony proxy:start
symfony proxy:domain:attach naarschoolinvlaanderen.be
symfony serve -d
```

## Deploy
```
symfony php ./vendor/bin/dep deploy production
```

## Frontend

```
yarn install
```

### Compile & watch assets
```
yarn encore dev --watch
```
### Build production assets
```
yarn encore production
```
## Tools

### Paslm
```
composer psalm
```

### PHPStan
```
composer phpstan
```

### Code style (Csfixer)

```
composer codestyle
```

### Generate baseline for Psalm and PHPStan

```
composer baseline
```
