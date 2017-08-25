<?php

	namespace WPKit\MagicMeta\Traits;
	
	trait IsMagic {
	
	    /**
	     * Get the magic meta of current instance
	     *
	     * @return array
	     */
	    public function getMagicMeta() 
	    {
		    return property_exists($this, 'magic_meta') ? $this->magic_meta : [];
	    }
	    
	    /**
	     * Get the magic meta keys of current instance
	     *
	     * @return array
	     */
	    public function getMagicMetaKeys() 
	    {
		    return array_keys($this->getMagicMeta());
	    }
	
		/**
	     * Get the magic meta keys of current instance
	     *
	     * @return array
	     */
	    public function getMagicMetaFlipped() 
	    {
		    return array_flip($this->getMagicMeta());
	    }
		    
	    /**
	     * Get a magic meta key of current instance
	     *
	     * @return array
	     */
	    public function getMagicMetaKey($key) 
	    {
		    $magic_meta = $this->getMagicMetaFlipped();
		    return ! empty($magic_meta[$key]) ? $magic_meta[$key] : null;
	    }
	    
	    /**
	     * Convert the model's attributes to an array.
	     *
	     * @return array
	     */
	    public function attributesToArray()
	    {
		    $attributes = parent::attributesToArray();
	        foreach ($this->getMagicMeta() as $meta_key => $key) {
	            $attributes[$key] = $this->getMeta($meta_key);
	        }
	        return $attributes;
	    }
	
	}
