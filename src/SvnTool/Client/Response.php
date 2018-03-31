<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 2018-03-31
 * Time: 11:55
 */

namespace SvnTool\Client;


class Response
{

    const HEADER_SIZE = 'HEADER_SIZE';

    protected $contentResponse;

    protected $headerInfo = [];

    protected $body;

    protected $revision;

    protected $folders;

    protected $url;

    protected $request;



    public function __construct( Request $request )
    {

        $this->request = $request;
        $this->body = $this->getRequest()->getResponseContent();

        if(is_resource($request->getResource())) {

            $this->headerInfo = [
              self::HEADER_SIZE => curl_getinfo( $request->getResource(), CURLINFO_HEADER_SIZE )
            ];

            $this->body = substr($this->body, $this->headerInfo[self::HEADER_SIZE]);
        }

    }



    /**
     * @return bool|string
     */
    public function getBody() {
        return $this->body;
    }



    /**
     * @return array
     */
    public function getFoldersArray() {
        $folders = [[], []];
        preg_match_all("|\shref=\"([a-zA-Z\.\-0-9]+)\/\"|s", $this->body, $folders, PREG_SPLIT_NO_EMPTY);
        return array_combine($folders[1], $folders[1]);
    }



    /**
     * @return int
     */
    public function getRevisionNumber() {
        return (int) preg_replace("/.*Revision\s([0-9]+).*/uis", "$1", $this->body);
    }



    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }




}