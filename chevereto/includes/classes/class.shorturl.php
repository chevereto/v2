<?php
/* --------------------------------------------------------------------

  Chevereto
  http://chevereto.com/

  @version	2.6.0
  @author	Rodolfo Berríos A. <http://rodolfoberrios.com/>
			<inbox@rodolfoberrios.com>

  Copyright (c) Rodolfo Berrios <inbox@rodolfoberrios.com>
  
  Licensed under the MIT license
  http://opensource.org/licenses/MIT

  --------------------------------------------------------------------- */
  
/**
 * class.shorturl.php
 * This class uses the most popular url shortening services to short a long url
 *
 * @author		Rodolfo Berríos <http://rodolfoberrios.com/>
 * @url			<http://chevereto.com>
 * @package		Chevereto
 *
 * @copyright	Rodolfo Berríos <inbox@rodolfoberrios.com>
 *
 */

class ShortURL {
	public $url;
	public $service;
	public $user;
    public $pass;
	
	public $custom_service_api;

	
    /**
     * get_ShortURL
     * Get the processed URL.
     *
     * @param	string
     * @return	string
     */	
	public function get_ShortURL($short_url) {
		$this->url = $short_url;
		switch($this->service) {
			case 'tinyurl':
				return $this->tinyurl($this->url);
			break;
			case 'google':
				return $this->google($this->url);
			break;
			case 'isgd':
				return $this->isgd($this->url);
			break;
			case 'bitly':
				return $this->bitly($this->url);
			break;
			case 'custom':
				return $this->custom($this->url);
			break;
		}
	}
	
	/**
	 * fetch_url
	 * Gets the url content for a given URL using the cURL library
	 *
     * @param	string
     * @return	string
	 */
	private function fetch_url($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 120);
		$file_get_contents = curl_exec($ch);
		curl_close($ch);
		return $file_get_contents;
	}
	
	
    /**
     * tinyurl
	 * http://tinyurl.com/
     *
     * @param	string
     * @return	string
     */
    private function tinyurl($url) {
        return $this->fetch_url('http://tinyurl.com/api-create.php?url='.urlencode($url));
    }
    
	
    /**
     * isgd
	 * http://is.gd
     *
     * @param	string
     * @return	string
     */
    private function isgd($url) {
		return $this->fetch_url('http://is.gd/api.php?longurl='.urlencode($url));
    }


    /**
     * bitly
	 * http://bit.ly
     *
     * @param	string
     * @return	string
     */
    private function bitly($url) {
        return $this->fetch_url('http://api.bit.ly/v3/shorten?login='.$this->user.'&apiKey='.$this->pass.'&longUrl='.urlencode($url).'&format=txt');
    }


    /**
     * custom
     *
     * @param	string
     * @return	string
     */
    private function custom($url) {
        return $this->fetch_url($this->custom_service_api.urlencode($url));
    }

	 
    /**
	 * Google functions thanks to Marcus Nunes - marcusnunes.com
	 *
     * @param	string
     * @return	string
     */
    private function google($url)
	{
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://www.googleapis.com/urlshortener/v1/url?key='.$this->pass);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    	curl_setopt($curl, CURLOPT_HEADER, 0);
    	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array('longUrl' => $url)));
        $return = curl_exec($curl);
        curl_close($curl);
        if($return) {
            $json = json_decode($return);
            return $json->id;
        }
    }

}
?>