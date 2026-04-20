<?php

declare(strict_types=1);

namespace Deployer;

use function \Deployer\import;

require 'config/deploy/tasks.php';

import('recipe/common.php');
import(__DIR__ . '/config/deploy/hosts.yaml');
import(__DIR__ . '/config/deploy/config.yaml');
