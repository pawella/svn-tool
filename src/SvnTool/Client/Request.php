<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 2018-03-31
 * Time: 12:15
 */

namespace SvnTool\Client;


class Request
{

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $user;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var Response;
     */
    protected $response;

    /**
     * @var resource
     */
    protected $resource;

    /**
     * @var
     */
    protected $responseContent = '';


    /**
     * Request constructor.
     * @param null $url
     */
    public function __construct($url = null)
    {
        $this->setUrl($url);
    }


    /**
     * @return Response
     */
    public function send() {

        if($this->url) {

            $this->resource = $resource = curl_init($this->url);

            curl_setopt($resource, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($resource, CURLOPT_VERBOSE, 1);
            curl_setopt($resource, CURLOPT_HEADER, 1);

            curl_setopt($resource, CURLOPT_HTTPHEADER, ['Content-Type: application/xml']);
            curl_setopt($resource, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 SvnTool/2.0.0.1");

            curl_setopt($resource, CURLOPT_HEADER, 1);

            if ( $authString = $this->getAuthString() ) {
                curl_setopt($resource, CURLOPT_USERPWD, $authString);
            }

            curl_setopt($resource, CURLOPT_TIMEOUT, 30);
            curl_setopt($resource, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($resource, CURLOPT_FOLLOWLOCATION, true);

            $this->responseContent = curl_exec( $resource );

            $this->response = new Response($this);

            return $this->response;

        }

        return new Response($this);

    }



    #region Getters/Setters

    /**
     * Return username:password
     * @return null|string
     */
    protected function getAuthString() {

        if( $this->user ||$this->password ) {
            return implode(':', [$this->user, $this->password]);
        }
        return null;
    }


    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return Request
     */
    public function setUrl($url)
    {

        $this->url = $url;

        if($url) {

            $authData = preg_replace("/^([a-z]{3,6}):\/\/(.*:{1}.*)@.*/", "$2", $url);

            if($authData) {

                $authDataArray = explode(':', $authData);

                if($this->password === null) {
                    $this->user = $authDataArray[0];
                }

                if($this->password === null) {
                    $this->password = (empty($authDataArray[1])) ? null : $authDataArray[1];
                }

                $this->url = str_replace($authData . '@', '', $url);

            }

        }

        return $this;

    }


    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $user
     * @return Request
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return Request
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return mixed
     */
    public function getResponseContent()
    {
        return $this->responseContent;
    }




    #endregion



}