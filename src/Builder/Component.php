<?php
declare(strict_types=1);

/**
 * This file is part of the Phalcon Developer Tools.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Phalcon\DevTools\Builder;

use Phalcon\Config;
use Phalcon\DevTools\Script\Color;
use Phalcon\DevTools\Validation\Validator\Namespaces;
use Phalcon\Validation;

/**
 * Base class for builder components
 */
abstract class Component
{
    /**
     * Builder Options
     *
     * @var Config
     */
    protected $options = null;

    /**
     * Path Component
     *
     * @var Path
     */
    protected $path;

    /**
     * Create Builder object
     *
     * @param array $options Builder options
     */
    public function __construct(array $options = [])
    {
        $this->options = new Config($options);
        $this->path = new Path(realpath('.') . DIRECTORY_SEPARATOR);
    }

    protected function checkNamespace($namespace)
    {
        $validation = new Validation();

        $validation->add('namespace', new Namespaces([
            'allowEmpty' => true
        ]));

        $messages = $validation->validate(['namespace' => $namespace]);

        if (count($messages)) {
            $errors = [];
            foreach ($messages as $message) {
                $errors[] = $message->getMessage();
            }

            throw new BuilderException(sprintf('%s', implode(PHP_EOL, $errors)));
        }

        return true;
    }

    /**
     * Tries to find the current configuration in the application
     *
     * @param string $type Config type: ini | php
     * @return Config
     * @throws BuilderException
     */
    protected function getConfig($type = null)
    {
        return $this->path->getConfig($type);
    }

    /**
     * Check if a path is absolute
     *
     * @param string $path Path to check
     *
     * @return bool
     */
    public function isAbsolutePath($path)
    {
        return $this->path->isAbsolutePath($path);
    }

    /**
     * Check if the script is running on Console mode
     *
     * @return boolean
     */
    public function isConsole()
    {
        return PHP_SAPI == 'cli';
    }

    /**
     * Check if the current adapter is supported by Phalcon
     *
     * @param  string $adapter
     *
     * @return bool
     * @throws BuilderException
     */
    public function isSupportedAdapter($adapter)
    {
        if (!class_exists('\Phalcon\Db\Adapter\Pdo\\' . $adapter)) {
            throw new BuilderException("Adapter $adapter is not supported");
        }

        return true;
    }

    /**
     * Shows a success notification
     *
     * @param string $message
     */
    protected function notifySuccess($message)
    {
        print Color::success($message);
    }

    abstract public function build();
}
