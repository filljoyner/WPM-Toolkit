<?php
namespace Wpm;


class WpmContainer {

    protected $classMap = [];      // default mappings
    protected $map = [];           // actual mappings
    protected $locations = [];
    protected $base = [];
    protected $store = [];
    protected $basePath = null;
    protected $baseUrl = null;


    public function __construct ($basePath, $baseUrl, $classMap)
    {
        $this->setStore('basePath', $basePath);
        $this->setStore('baseUrl', $baseUrl);

        $this->setStore('packPath', $basePath . '/pack');
        $this->setStore('packUrl', $baseUrl . '/pack');

        $this->setStore('imageCacheDir', get_template_directory() . '/resources/cache/images');
	    $this->setStore('imageCacheUrl', get_template_directory_uri() . '/resources/cache/images');

        $this->classMap = $classMap;
    }


	/**
	 * Parses the selector string and passes data into the selected Package or Pack
	 *
	 * @param $selector
	 *
	 * @return mixed
	 */
	public function resolve($selector)
    {
        $selections = $this->parseSelector($selector);
        $select = $selections[0];

        $type = 'handlers';
        $class = $this->resolveClass('handlers', $select);

        if(!$class) {
            $type = 'packs';
            $class = $this->resolveClass('packs', $select);
        }

        if ( empty( $this->maps[$type][ $select ] ) ) {
            $this->map[$type][ $select ] = new $class;
        }

        return $this->map[$type][ $select ]->handle ( $selections[1] );
    }


    /**
     * Parses the wpm string to usable parts.
     *
     * @param $string
     *
     * @return array
     */
    private function parseSelector ( $string )
    {
        $parts = explode ( '.', $string );
        $args = [ $parts[0] ];

        if ( isset( $parts[1] ) ) {
            $args[] = explode ( '|', $parts[1] );
        } else {
            $args[] = [];
        }

        return $args;
    }



    /**
     * Return the correct class from the class map by a given key.
     *
     * @param $type
     * @param $key
     *
     * @return bool|mixed
     */
    private function resolveClass ( $type, $key )
    {
        $classMap = $this->classMap ( $type );

        if ( isset( $classMap[ $key ] ) ) {
            return $classMap[ $key ];
        }

        return false;
    }




    /**
     * Receives a string, resolves its associated class from the classMap,
     * adds it to the wpmClassMap global, and returns the wpmClassMap.
     *
     * @param $type
     *
     * @return array
     */
    private function classMap ( $type )
    {
        if ( $type ) {
            if ( !empty( $this->classMap[ $type ] ) ) {
                return $this->classMap[ $type ];
            }
        }

        return false;
    }


	/**
	 * Returns a variable from the container if it has been set
	 *
	 * @param $key
	 *
	 * @return mixed|null
	 */
	public function getStore($key)
    {
        if (isset($this->store[$key])) {
            return $this->store[$key];
        }

        return null;
    }


	/**
	 * Stores a variable in the container
	 *
	 * @param $key
	 * @param null $value
	 */
	public function setStore($key, $value=null)
    {
        $this->store[ $key ] = $value;
    }

}
