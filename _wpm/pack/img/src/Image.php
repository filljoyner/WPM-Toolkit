<?php
namespace WpmPack\Img\src;

use Intervention\Image\ImageManagerStatic as iImage;

class Image
{
	protected $id = null;                   // id of image attachment
	protected $image = null;                // an instance of the image being build
	protected $cacheUrl = null;             // the base url to the image cache
	protected $cachePath = null;            // the base path to the image cache
	protected $imageName = null;            // the name of the image to be saved to the cache
	protected $mediaFilePath = null;        // the original image file path

	protected $method = null;               // the method used to create the new image (fit or resize)
    protected $namePrefix = null;           // the prefix added to the imageName when creating cached version
	protected $width = null;                // the defined width of the new image
	protected $height = null;               // the defined height of the new image
	protected $quality = 95;                // the quality of the image saved to the cache
	protected $closure = null;              // the closure of settings sent when building an image
	protected $upscale = false;             // should the image scale up beyond its natural limits

	protected $buildFileName;               // the filename for the image being created in the cache
	protected $buildCachedFileNamePath;     // the path to the file being created in the cache
	protected $buildCachedFileNameUrl;      // the url to the file being created in the cache


    /**
     * Img constructor receives the cache path and cache url
     *
     * @param $cachePath
     * @param $cacheUrl
     */
    public function __construct($cachePath, $cacheUrl)
	{
		$this->cachePath = $cachePath;
        $this->cacheUrl = $cacheUrl;
	}


    /**
     * Pass in the id to an image stored in the media library and return
     * the results of the media method.
     *
     * @param $id
     *
     * @return bool|Img
     */
    public function id($id)
	{
		return $this->media($id);
	}


    /**
     * Pass in the source file url of an image stored in the media library
     * and return the results of the media method.
     *
     * @param $src
     *
     * @return bool|Img
     */
    public function src($src)
	{
		return $this->media($src);
	}


    /**
     * Set class properties based on the received media
     *
     * @param $media
     *
     * @return $this|bool
     */
    public function media($media)
	{
		if(is_numeric($media)) {
			$this->id = $media;
			$media = wp_get_attachment_url($media);
		}

        $wpUploadDir = wp_upload_dir();


		if(current_theme_supports('soil-relative-urls')) {
			$relMediaPath = str_replace(home_url(), '', $wpUploadDir['baseurl']);
            $this->mediaFilePath = $wpUploadDir['basedir'];
			$this->mediaFilePath.= str_replace($relMediaPath, '', $media);
		} else {
		    $this->mediaFilePath = str_replace($wpUploadDir['baseurl'], $wpUploadDir['basedir'], $media);
        }

		if(!$this->mediaFilePath) return false;

        $this->imageName = basename($this->mediaFilePath);
        
		return $this;
	}


    /**
     * Setter for the upscale property
     *
     * @param bool $upscale
     *
     * @return $this
     */
    public function upscale($upscale=true)
	{
		$this->upscale = $upscale;
		return $this;
	}


    /**
     * Setter for the quality property
     *
     * @param $quality
     *
     * @return $this
     */
    public function quality($quality)
	{
		$this->quality = $quality;
		return $this;
	}


    /**
     * Set properties in preparation for a fit image creation. While we call it "fit"
     * the method used to obtain this image from intervention is called "resize."
     *
     * @param null $w
     * @param null $h
     *
     * @return $this
     */
    public function fit($w=null, $h=null)
	{
		$this->method = 'resize';
        $this->namePrefix = 'fit';
		$this->width = $w;
		$this->height = $h;
        $this->closure = function ($constraint) {
            $constraint->aspectRatio();
			if(! $this->upscale) $constraint->upsize();
		};
		return $this;
	}


	/**
	 * Set properties in preparation for a resize image creation. While we call it "resize"
     * the method used to obtain this image from intervention is called "fit."
	 *
     * @param null $w
     * @param null $h
     *
     * @return $this
     */
    public function resize($w=null, $h=null)
	{
		$this->method = 'fit';
        $this->namePrefix = 'resize';
		$this->width = $w;
		$this->height = $h;
		$this->closure = function ($constraint) {
            $constraint->aspectRatio();
			if(! $this->upscale) $constraint->upsize();
		};
		return $this;
	}


    /**
     * Receive options, build the image into cache and return an image element
     * based on the received options
     *
     * @param array $options
     *
     * @return string
     */
    public function get($options=[])
	{
		$this->buildImage();
		return $this->buildImageElement($options);
	}


    /**
     * Build the image into cache and return the cache file url
     * @return mixed
     */
    public function url()
	{
		$this->buildImage();
		return $this->buildCachedFileNameUrl;
	}


    /**
     * Build the image and store it in cache based on the current properties
     */
    protected function buildImage()
	{
		$this->buildFileName = $this->buildFileName();
		$this->buildCachedFileNamePath = $this->cachePath . '/' . $this->buildFileName;
		$this->buildCachedFileNameUrl = $this->cacheUrl . '/' . $this->buildFileName;

		if(!file_exists($this->buildCachedFileNamePath)) {
			$this->image = iImage::make($this->mediaFilePath);

			$this->image->{$this->method}($this->width, $this->height, $this->closure);

			$this->image->save($this->buildCachedFileNamePath, $this->quality);
		}
	}


    /**
     * Return the file name as it will appear in the cache.
     *
     * @return string
     */
    protected function buildFileName()
	{
		$upscale = $this->upscale ? 'upscale' : 'noupscale';
		return $this->namePrefix . '-' . $this->width . 'x' . $this->height . '-' . $upscale . '-' . $this->quality . '-' . $this->imageName;
	}


    /**
     * Build and return an image element with provided options
     *
     * @param $options
     *
     * @return string
     */
    protected function buildImageElement($options)
	{
		if($this->id and empty($options['alt'])) {
			$options['alt'] = esc_attr(get_post_meta($this->id, '_wp_attachment_image_alt', true));
		}

		$img = '<img src="' . $this->buildCachedFileNameUrl . '" ';
		foreach($options as $attr => $value) $img.= $attr . '="' . $value . '" ';
		$img.= '/>';

		return $img;
	}

}
