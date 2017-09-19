<?php

	namespace WPKit\MagicMeta\Traits;
	
	trait IsMagic {
		
		/**
	     * @var array
	     */
		protected $committed_meta = [];
		
		/**
	     * Boot the model
	     *
		 */
		public static function boot()
	    {
	        parent::boot();
	
	        static::saved(function($model)
	        {
	            foreach($model->getCommitedMeta() as $key => $meta_value) {
		            $model->updateMetaValue( $model->getMagicMetaKey( $key ), $value );
	            }
	            $this->clearCommit();
	        });
	    }
	    
	    /**
	     * Get the meta that has been commited for save
	     *
	     * @return array
	     */
	    public function getCommitedMeta() 
	    {
			return property_exists($this, 'committed_meta') ? $this->commited_meta : [];
	    }
	    
	    /**
	     * Commit meta for save
	     *
	     * @param string $key 
	     * @param string $value
	     * @return void
	     */
	    public function commitMeta($key, $value) 
	    {
			$this->commited_meta[$key] = $value;
	    }
	    
	    /**
	     * Clear commit meta for save
	     *
	     * @return void
	     */
	    public function clearCommit() 
	    {
			$this->commited_meta = [];
	    }
		
		/**
	     * Fill the model with an array of attributes.
	     *
	     * @param  array  $attributes
	     * @return $this
	     *
	     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
	     */
	    public function fill(array $attributes)
	    {
	        parent::fill($attributes);
	        foreach($this->fillableFromMagicMeta($attributes) as $key => $value) {
		        $this->commitMeta($key, $value);
	        }
	        return $this;
	    }
	
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
	    
	    /**
	     * Get the commitable magic meta of a given array.
	     *
	     * @param  array  $attributes
	     * @return array
	     */
	    public function fillableFromMagicMeta(array $attributes) 
	    {
		    return array_intersect_key($attributes, $this->getMagicMeta());
	    }
	
	}
