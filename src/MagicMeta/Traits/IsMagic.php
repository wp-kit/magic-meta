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
		
		/**
	     * Get Meta
	     * This method is agnostic to the Meta model, as this trait can be used on Post, Category and User models
	     *
	     * @return string
	     */
		public function getMeta($meta_key) 
		{			
			return $this->meta()->where('meta_key', $meta_key);	
		}
	    
	    /**
	     * Where query
	     *
	     * @return QueryBuilder
	     */
		protected function scopeTransformQuery( Builder $query, Request $request ) {
				
			if( ! empty( $request->get('s') ) ) {
				
				$query->where( 'post_title', 'like', '%' . $request->get('s') . '%' );
				
				foreach($this->getMagicMeta() as $meta_key => $key) {
					
					$query->join(
		    			'postmeta as ' . $key, 
		    			function($join) use($key, $meta_key) {
					        $join->on($key . '.post_id', '=', 'ID');
					        $join->where($key . '.meta_key', '=', $meta_key);
					    }
		    		);
					
					$query->orWhere( $meta_key . '.meta_value', 'like', '%' . $request->get('s') . '%' );
					
				}
				
			}
			
			if( ! empty( $request->get('meta_query') ) ) {
			
				foreach($request->get('meta_query') as $meta_query) {
					
					if( empty( $request->get('s') ) ) {
					
						$query->join(
			    			'postmeta as ' . $meta_query['key'], 
			    			function($join) use($query, $meta_query) {
						        $join->on($meta_query['key'] . '.post_id', '=', 'ID');
						        $join->where($meta_query['key'] . '.meta_key', '=', $meta_query['key']);
						    }
			    		);
			    		
			    	}
			    	
		    		$query->where(
		    			$meta_query['key'] . '.meta_value', 
		    			! empty( $meta_query['compare'] ) ? $meta_query['compare'] : '=', 
		    			$meta_query['value']
		    		);
					
				}
				
			}

			if( ! empty( $request->get('tax_query') ) ) {
				
				$tax_queries = array_map(function($tax_query) {
					
					return ! is_array( $tax_query ) ? json_decode(stripslashes($tax_query), true) : $tax_query;
					
				}, $request->get('tax_query'));
				
				$tax_queries = array_filter($tax_queries, function($tax_query) {
					
					return ! empty( $tax_query['values'] ) || ! empty( $tax_query['values'] );
					
				});
				
				foreach($tax_queries as $tax_query) {
					
					$abbrev_tr = ! empty( $meta_query['taxonomy'] ) ? $meta_query['taxonomy'] : 'category';
					
					$query->join(
		    			'term_relationships as ' . $abbrev_tr, 
		    			$abbrev_tr . '.object_id', '=', 'posts.ID'
		    		);
		    		
		    		$abbrev_tt = $abbrev_tr . '_taxonomy';
		    		
		    		$query->join(
		    			'term_taxonomy as ' . $abbrev_tt, 
		    			$abbrev_tt . '.term_taxonomy_id', '=', $abbrev_tr . '.term_taxonomy_id'
		    		);
		    		
		    		$abbrev_t = $abbrev_tr . '_terms';
		    		
		    		$query->join(
		    			'terms as ' . $abbrev_t, 
		    			$abbrev_t . '.term_id', '=', $abbrev_tt . '.term_id'
		    		);
		    		
		    		$query->where($abbrev_tt . '.taxonomy', $abbrev_tr);
		    		
		    		if( ! empty( $tax_query['values'] ) ) {
		    		
			    		if( ! is_array( $tax_query['values'] ) ) {
				    	
			    			$query->whereIn( $abbrev_t . '.term_id', $tax_query['values'] );
			    			
			    		} else {
				    		
				    		$query->where( $abbrev_t . '.term_id', $tax_query['values'] );
				    		
			    		}
			    		
			    	} else {
				    	
				    	$query->where( $abbrev_t . '.term_id', $tax_query['value'] );
				    	
			    	}
					
				}
				
			}
			
			if( $magic_meta = array_filter( $request->only( $this->getMagicMeta() ) ) ) {
				
				foreach($magic_meta as $key => $meta) {
					
					$meta = is_array($meta) ? $meta : [
						'compare' => '=',
						'value' => $meta
					];
					
					if( empty( $request->get('s') ) ) {
						
						$meta_key = $this->getMagicMetaKey( $key );
					
						$query->join(
			    			'postmeta as ' . $key, 
			    			function($join) use($key, $meta_key) {
						        $join->on($key . '.post_id', '=', 'ID');
						        $join->where($key . '.meta_key', '=', $meta_key);
						    }
			    		);
			    		
			    	}
			    	
		    		$query->where(
		    			$key . '.meta_value', 
		    			! empty( $meta['compare'] ) ? $meta['compare'] : '=', 
		    			$meta['value']
		    		);
					
				}
				
			}
		
			return $query;
			
		}
	
	}
