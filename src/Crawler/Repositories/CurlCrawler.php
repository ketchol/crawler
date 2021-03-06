<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 7/11/2017
 * Time: 6:16 PM
 */

namespace IvanCLI\Crawler\Repositories;


use IvanCLI\Crawler\Contracts\CrawlerContract;
use Ixudra\Curl\Facades\Curl;

class CurlCrawler extends CrawlerContract
{
    /**
     * Load content
     * @return \stdClass
     */
    public function fetch()
    {
        $curler = Curl::to($this->url)
            ->withHeaders($this->headers)
            ->returnResponseObject()
            ->withOption("FOLLOWLOCATION", true);

        if (!is_null($this->cookies_path)) {
            $curler = $curler->setCookieFile($this->cookies_path);
            $curler = $curler->setCookieJar($this->cookies_path);
        }

        if (!is_null($this->request_data)) {
            $curler = $curler->withData($this->request_data);
        }

        if ($this->json_request === true) {
            $curler = $curler->asJsonRequest();
        }

        if ($this->json_response === true) {
            $curler = $curler->asJsonResponse();
        }

        if (!is_null($this->ip) && !is_null($this->port)) {
            $curler = $curler->withOption('PROXY', $this->ip)->withOption('PROXYPORT', $this->port);
        }

        if (!is_null($this->referer)) {
            $curler = $curler->withOption('REFERER', $this->referer);
        }

        switch ($this->request_type) {
            case 'POST':
                $response = $curler->post();
                break;
            case 'PUT':
                $response = $curler->put();
                break;
            case 'PATCH':
                $response = $curler->patch();
                break;
            case 'DELETE':
                $response = $curler->delete();
                break;
            case 'GET':
            default:
                $response = $curler->get();

        }
        $this->status = $response->status;
        $this->content = $response->content;
        return $response;
    }
}