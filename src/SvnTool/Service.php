<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 2018-03-31
 * Time: 10:01
 */

namespace SvnTool;

use SvnTool\Client\Request;
use SvnTool\Client\Response;
use Zend\Cache\Storage\StorageInterface;

class Service
{

    protected $packages;

    protected $repositories;

    protected $requests = [];

    /**
     * @var StorageInterface
     */
    protected $cache;

    /**
     * @param array $repositories
     * @return $this
     */
    public function setRepositories(array $repositories)
    {
        $this->repositories = $repositories;
        return $this;
    }


    #region Request


    protected function getInfoBy($url, &$packages)
    {
        $svnInfoContent = [];

        $response = $this->request($url);

        $sourceUrl = $response->getRequest()->getUrl();
        $folders = $response->getFoldersArray();

        if (!empty($folders['trunk'])) {

            $reference = '/trunk';

            if ($composerArray = $this->getComposerJsonArray($url, $reference)) {

                $packages[$composerArray['name']][$composerArray['version']] = $this->buildPackage($composerArray, $sourceUrl, $reference, $response);

            }

        }


        if (!empty($folders['tags'])) {

            $response = $this->request($url . '/tags');

            if ($folders = $response->getFoldersArray()) {

                foreach ($folders as $tag) {

                    $reference = '/tags/' . $tag;

                    if ($composerArray = $this->getComposerJsonArray($url, $reference)) {

                        $packages[$composerArray['name']][$composerArray['version']] = $this->buildPackage($composerArray, $sourceUrl, $reference, $response);

                    }
                }

            }
        }

        return $svnInfoContent;

    }


    protected function buildPackage($composerArray, $repositoryUrl, $referencePath, Response $response)
    {

        $package = [
            'name' => $composerArray['name'],
            'version' => $composerArray['version'],
            'source' => [
                'type' => 'svn',
                'url' => $repositoryUrl,
                'reference' => $referencePath . '/@' . $response->getRevisionNumber(),
                'svn-cache-credentials' => false
            ]
        ];

        if(!empty($composerArray['autoload'])) {
            $package['autoload'] = $composerArray['autoload'];
        }

        if(!empty($composerArray['require'])) {
            $package['require'] = $composerArray['require'];
        }

        return $package;

    }


    /**
     * @param $url
     * @param $dir
     * @return array|null
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function getComposerJsonArray( $url, $dir )
    {

        $response = $this->request($url . $dir . '/composer.json');
        $data = (array) json_decode($response->getBody(), JSON_ERROR_NONE);
        return (!empty($data['name']) && !empty($data['version'])) ? $data : null;

    }


    /**
     * @return null|array
     */
    public function getPackagesArray() {

        if(is_array($this->repositories)) {

            if(null === $this->packages) {

                $packages = [];

                foreach ($this->repositories as $key => $repository) {
                    $this->getInfoBy($repository, $packages);
                }

                $this->packages = $packages;

            }

            return $this->packages;

        }

        return null;
    }


    /**
     * @param $url
     * @return Response
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    protected function request( $url )
    {

        $urlToken = md5((string) $url);

        if( $cache = $this->getCache() ) {

            $test = null;
            $request = $cache->getItem($urlToken, $test);

            if( $request ) {
                $this->requests[$urlToken] = $request;
            }

        }

        if(!empty($this->requests[$urlToken])) {

            $request = $this->requests[$urlToken];

        } else {

            $request = new Request($url);
            $request->send();

            if( $cache = $this->getCache() ) {
                $cache->addItem($urlToken, $request);
            }

        }

        return $request->getResponse();

    }

    #endregion

    public function responseJsonPackage( $option = JSON_UNESCAPED_SLASHES)
    {
        header('Content-Type: application/json');
        echo json_encode(['packages' => $this->getPackagesArray()], JSON_PRETTY_PRINT | $option);
        exit();
    }

    /**
     * @return mixed
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     * @param mixed $packages
     */
    public function setPackages($packages)
    {
        $this->packages = $packages;
    }

    /**
     * @return array
     */
    public function getRequests()
    {
        return $this->requests;
    }

    /**
     * @param array $requests
     */
    public function setRequests($requests)
    {
        $this->requests = $requests;
    }

    /**
     * @return StorageInterface
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param StorageInterface $cache
     */
    public function setCache( StorageInterface $cache)
    {
        $this->cache = $cache;
    }




}