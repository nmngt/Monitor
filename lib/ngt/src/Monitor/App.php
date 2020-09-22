<?php //declare(strict_types=1);

namespace NGT\Monitor;

use GuzzleHttp\Psr7;
use Katzgrau\KLogger\Logger;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Console\Style\SymfonyStyle;

use NGT\Registry;
use NGT\Monitor\Config;
use NGT\Monitor\Contracts\Constants;
use Symfony\Component\Dotenv\Dotenv;

use NGT\Monitor\Exceptions\RuntimeException;
use NGT\Monitor\Exceptions\ClientResponseException;
use NGT\Monitor\Exceptions\ClientSslResponseException;
use NGT\Monitor\Exceptions\ClientDnsResponseException;

class App
{
    protected $io;
    protected $basePath;
    protected $storagePath;
    protected $config;
    protected $sites;
    protected $client;
    private $errorCount;

    public function __construct($basePath = null)
    {
        if ($basePath) {
            $this->setBasePath($basePath);
        }

        // load core application configuration
        try {
            $dotenv = new Dotenv();
            $dotenv->load($this->basePath.'/.env');
        } catch (RuntimeException $e) {
            printf('Unable to parse the .env file: %s', $e->getMessage());
        }

        // load checks configuration
        try {
            $this->sites = SitesConfig::get([
                'checks' => require $this->configPath("checks.php"),
                'sites' => require $this->configPath("sites.php")
            ]);
        } catch (RuntimeException $e) {
            printf('Unable to parse the config files: %s', $e->getMessage());
        }
    }

    public function __destruct()
    {
        if (MONITOR_MODE == 'cli') {
            echo "\n\n";
            echo __METHOD__;
            echo "\n";
        }
    }

    public function __debugInfo()
    {
        return [
            'client' => $this->client,
            // 'status' => $this->status,
            'config' => $this->config,
            'sites' => $this->sites,
            'basePath' => $this->basePath,
            'storagePath' => $this->storagePath,
            'errorCount' => $this->errorCount,
        ];
    }

    /**
     * run the app with configured sites and checks
     *
     * @return [type] [description]
     */
    public function runConfigChecks(bool $sendReport)
    {
        if (MONITOR_MODE == 'cli') {
            $this->io->section(Constants::PROPAGANDA." v.".Constants::VERSION." running checks:config");
        }

        foreach ($this->sites as $site) {
            $url = $site->getUrl();

            if (MONITOR_MODE == 'cli') {
                $this->io->section('Processing '.$url);
            }

            // Add Status object
            Registry::add($url, new Status($url));

            // scrape
            $site->setResponse($this->getSiteResponse($url));

            // run checks
            $this->runChecks($url, $site);

            if (MONITOR_MODE == 'cli') {
                $this->io->newline();
            }
        }

        if ($sendReport) {
            $this->report('config');
        }

        return $this;
    }

    /**
     * run the app with just the given url/domain
     *
     * @param  string $url [description]
     * @return [type]      [description]
     */
    public function runSiteChecks(string $url, array $checks = Constants::DEFAULT_CHECKS)
    {
        if (MONITOR_MODE == 'cli') {
            $this->io->section(Constants::PROPAGANDA." v.".Constants::VERSION." running checks:site -> ".$url);
        }

        // Add Status object
        Registry::add($url, new Status($url));

        $site = new Site($url, $checks);
        $site->setResponse($this->getSiteResponse($url));

        $this->runChecks($url, $site);
    }

    /**
     * run the app with configured sites and only ssl checks
     *
     * @return [type] [description]
     */
    public function runSslChecks(bool $sendReport, array $checks = Constants::SSL_CHECKS)
    {
        if (MONITOR_MODE == 'cli') {
            $this->io->section(Constants::PROPAGANDA." v.".Constants::VERSION." running checks:ssl");
        }

        foreach ($this->sites as $site) {
            $url = $site->getUrl();

            if (MONITOR_MODE == 'cli') {
                $this->io->note('Processing '.$url);
            }

            // Add Status object
            Registry::add($url, new Status($url));

            // scrape
            $site->setResponse($this->getSiteResponse($url));

            // run checks
            $this->runChecks($url, $site, Constants::SSL_CHECKS);

            if (MONITOR_MODE == 'cli') {
                $this->io->newline();
            }
        }

        if ($sendReport) {
            $this->report('ssl');
        }

        return $this;
    }

    /**
     * gets all the needed informations from remote site.
     * including http, dns & ssl informations.
     *
     * @param  string $url [description]
     * @return Client      [description]
     */
    private function getSiteResponse(string $url)
    {
        try {
            $this->client[$url] = new Client($url);

            $this->log([
                'url' => $url,
                'context' => Constants::SUCCESS,
                'class' => get_class($this->client[$url]),
                'msg' => 'Resource successfully loaded',
                'value' => 1,
            ]);
        } catch (ClientResponseException | ClientSslResponseException | ClientDnsResponseException $e) {
            $this->errorCount++;
            $this->client[$url] = false;
            $this->log([
                'url' => $url,
                'context' => Constants::ERROR,
                'class' => 'NGT\Monitor\Client',
                'msg' => $e->getMessage(),
                'value' => 1,
            ]);
        }

        return $this->client[$url];
    }

    /**
     * run the checks
     *
     * @param  string $url  [description]
     * @param  Site   $site [description]
     * @return [type]       [description]
     */
    private function runChecks(string $url, Site $site, $checksToRun = null)
    {
        $checksToRun = ! is_null($checksToRun) ? $checksToRun : $site->getChecks();

        if (false !== $site->getResponse($url)) {

            $checks = new Checks(
                $checksToRun, // ..checks from param or from config for this site
                $this->sites->getCheckDefinitions() // get default check definitions
            );

            // run all configured checks for this site
            foreach ($checks->build() as $checkName => $check) {

                $check->setClient($site->getResponse());
                $result = $check->validate();

                if (! $result) {

                    $this->errorCount++;

                    $this->log([
                        'url' => $url,
                        'context' => Constants::ERROR,
                        'class' => $check->getClassName(),
                        'msg' => $check->getErrorMessage(),
                        'value' => $check->getReturnValue(),
                    ]);
                } else {
                    $this->log([
                        'url' => $url,
                        'context' => Constants::SUCCESS,
                        'class' => $check->getClassName(),
                        'msg' => $check->getSuccessMessage(),
                        'value' => $check->getReturnValue(),
                    ]);
                }
            }
            return $this;
        }
        return false;
    }

    /**
     * sending reports via mail
     *
     * @param  string $context [description]
     * @return [type]          [description]
     */
    private function report(string $context)
    {
        $report = new Report($context);

        $html = $report->getHtml();


        if (MONITOR_MODE == 'cli') {
            $this->io->text("Sending the report to ".$_ENV['MAIL_TO'].': ');
        }

        if ($report->send()) {
            if (MONITOR_MODE == 'cli') {
                $this->io->text('Done.');
            }
            return true;
        } else {
            if (MONITOR_MODE == 'cli') {
                $this->io->text('Failed.');
                $this->io->error('Could not send email report!');
            }
            return false;
        }
    }

    //
    // Helpers
    //

    public function log(array $data)
    {
        $icon = ($data['context'] != "error") ? "✔︎" : "✘";
        $outputContext = ($data['context'] == "error") ? "error" : "text";


        // add status
        if (Registry::load($data['url'])) {
            Registry::load($data['url'])->add($data, $data['context']);
        }

        // print to console
        if (MONITOR_MODE == 'cli') {
            $this->io->{$outputContext}($icon." [{$data['class']}] {$data['msg']}");
        }
    }



    //
    // Getters & Setters
    //

    public function getStatus()
    {
        return $this->status;
    }

    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function setIO(SymfonyStyle $io)
    {
        $this->io = $io;
    }

    public static function io()
    {
        return self::$io;
    }

    //
    // CORE FRAMEWORK
    //

    public function version()
    {
        return Constants::VERSION;
    }

    /**
     * Set the base path for the application.
     *
     * @param  string  $basePath
     * @return $this
     */
    public function setBasePath($basePath)
    {
        $this->basePath = rtrim($basePath, '\/');

        $this->bindPaths();

        return $this;
    }

    /**
     * Bind all of the application paths in the container.
     *
     * @return void
     */
    protected function bindPaths()
    {
        $this->storagePath = $this->storagePath();
    }

    /**
     * Get the base path of the Laravel installation.
     *
     * @param  string  $path Optionally, a path to append to the base path
     * @return string
     */
    public function basePath($path = '')
    {
        return $this->basePath.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the path to the application configuration files.
     *
     * @param  string  $path Optionally, a path to append to the config path
     * @return string
     */
    public function configPath($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'config'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the path to the public / web directory.
     *
     * @return string
     */
    public function publicPath()
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'public';
    }

    /**
     * Get the path to the storage directory.
     *
     * @return string
     */
    public function storagePath($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'storage'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Set the storage directory.
     *
     * @param  string  $path
     * @return $this
     */
    public function useStoragePath($path)
    {
        $this->storagePath = $path;

        return $this;
    }

    /**
     * Determine if the application is currently down for maintenance.
     *
     * @return bool
     */
    public function isDownForMaintenance()
    {
        return file_exists($this->storagePath().'/down');
    }
}
