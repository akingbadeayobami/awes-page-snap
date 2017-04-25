<?php

require_once 'lib/simpleHtmlDom.php';

class AwesPageSnap{

	public function __construct($url){

    $this->url = $url;

    $this->getURLHost();

    $this->makeDir();

		$this->getDOM();

    $this->findCSS();

    $this->findJS();

    $this->findIMG();

    $this->saveFile();

	}

  private function saveFile(){

    file_put_contents("{$this->folder}/index.html",$this->blob);

  }

  private function findCSS(){

    $links = $this->dom->find('link');

    foreach($links as $link){

      if($link->rel == 'stylesheet'){

        echo $href = $this->sanitizeUrl($link->href);

        $content = file_get_contents($href);

        preg_match("/(?:\w+)\.css/i",urldecode($href) ,$match);

        if($match){

          $fileName = $match[0];

        }else{

          $fileName = rand(100000,9999999) . ".css";

        }

        $content = $this->processCSS($content);

        file_put_contents("./assets/css/$fileName",$content);

        $this->blob = str_replace($link->href,"./assets/css/$fileName",$this->blob);

        echo "\n";

      }

    }

  }

  private function processCSS($input){

    // look for all fonts store them

    // look for all images

    // look for import styles;

    return $input;


  }

  private function findJS(){

    $scripts = $this->dom->find('script');

    foreach($scripts as $script){

      if($script->src){

        echo $src = $this->sanitizeUrl($script->src);

        $content = file_get_contents($src);

        preg_match("/(?:\w+)\.js/i",urldecode($src) ,$match);

        if($match){

          $fileName = $match[0];

        }else{

          $fileName = rand(100000,9999999) . ".js";

        }

        file_put_contents("./assets/js/$fileName",$content);

        $this->blob = $this->blob = str_replace($script->src,"./assets/js/$fileName",$this->blob);

        echo "\n";

      }

    }


  }

  private function findIMG(){

    $imgs = $this->dom->find('img');

    foreach($imgs as $img){

      if($img->src){

        echo $src = $this->sanitizeUrl($img->src);

        $content = file_get_contents($src);

        preg_match("/(?:\w+)\.(png|jpeg|jpg|gif)/i",urldecode($src) ,$match);

        if($match){

          $fileName = $match[0];

        }else{

          $fileName = rand(100000,9999999) . ".jpg";

        }

        file_put_contents("./assets/img/$fileName",$content);

        $this->blob = $this->blob = str_replace($img->src,"./assets/js/$fileName",$this->blob);

        echo "\n";

      }

    }


  }

	private function getURLHost(){

   $parsedUrl = parse_url($this->url); // We need to parse the url so as to get the base url to prevent external links

   $this->urlHost = $parsedUrl['host']; // here we get the base url

  }

  private function makeDir (){

      $this->folder = "Pages/{$this->urlHost}";

      $this->assets = "{$this->folder}/assets";

      if(!file_exists($this->folder)){

        mkdir("{$this->folder}");

        fclose($myfile);

        mkdir("{$this->assets}");

        mkdir("{$this->assets}/css");

        mkdir("{$this->assets}/js");

        mkdir("{$this->assets}/img");

        mkdir("{$this->assets}/fonts");


      }


       // $txt = "John Doe\n";
       // fwrite($myfile, $txt);



    }

  private function sanitizeUrl($link){

      $link = strtolower($link); // String to lower to allow uniformity

      if (substr($link, strlen($link) - 1 , 1) == '/'){ // (http://facebook.com/) => {http://facebook.com}

          $link = substr($link, 0, strlen($link) - 1 ); // removes all trailing slashes

      }

      if(strpos($link, "#")){ // (home.php#bottom) => {home.php}

        $link = substr($link, 0 , strpos($link, "#")); // Removes all ID references since they are not needed

      }

      if(substr($link, 0, 1) == "."){ // (./home.php) => {home.php}

        $link = substr($link, 1); // Removes the parent directory navigator

      }

      if (substr($link, 0 ,7) == "http://" ||  substr($link, 0 ,8) == "https://"){  // Seems Pretty obvious but it is needed since to remove it from the grand else at the bottom

        $link = $link;

      }else if(substr($link, 0, 2) == "//"){ // (//facebook.com) => {http://facebook.com}

        $link = 'http://' . $link; //append https to the link

      } else if(substr($link, 0, 1) == "#"){ // (#) => {thisurl.com}

        $link = $this->url;

      }else if (substr($link, 0 ,7) == "mailto:"){ // mails

        $link = "[" . $link . "]";

      } else if (substr($link, 0, 1) != "/"){ // appends full url to root relative paths (index.php) => {thisurl.com/index.php}

        $link = $this->url . "/" . $link;

      }else{

        $link = $this->url . $link;

      }

      return $link;

    }

  private function getDOM(){

    $this->blob = file_get_contents($this->url);

    $this->dom = str_get_html($this->blob);

  }

}

new AwesPageSnap(YOUR_SITE.COM);
