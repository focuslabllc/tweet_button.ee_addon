<?php
// ini_set('display_errors',E_ALL);

/**
 * Tweet Button
 * 
 * This file must be placed in the
 * system/plugins/ folder in your ExpressionEngine 1.x installation.
 * OR
 * system/expressionengine/third_party/tweet_button in your ExpressionEngine 2.x installation
 *
 * @package TweetButton
 * @version 1.0.0
 * @author Focus Lab, LLC http://focuslabllc.com
 * @copyright Copyright (c) 2010 Focus Lab, LLC
 * @see http://focuslabllc.com/software/tweet-button/
 */

$plugin_info       = array(
	'pi_name'        => 'Tweet Button',
	'pi_version'     => '1.0.0',
	'pi_author'      => 'Focus Lab, LLC / Erik Reagan',
	'pi_author_url'  => 'http://focuslabllc.com',
	'pi_description' => 'Easily add Twitter\'s official Tweet Button to your EE templates',
	'pi_usage'       => Tweet_button::usage()
	);

class Tweet_button
{
	
	var $return_data    = '';
	var $html           = '';
	var $TMPL           = NULL;
	
	// Set our default values
	var $class          = 'twitter-share-button';
	var $count          = FALSE;
	var $via            = FALSE;
	var $related        = FALSE;
	var $related_desc   = FALSE;
	var $tweet_text     = FALSE;
	var $url            = FALSE;
	var $lang           = FALSE;
	var $exclude_js     = FALSE;
	var $wrap_a         = FALSE;
	var $anchor_text    = 'Tweet';
	var $js             = '<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>';
	
	
	/**
	 * Our constructor and primary function
	 * This builds the tweet button based on parameters and default settings
	 *
	 * @return		string
	 */
	function Tweet_button()
	{
		
		// Check to see what version of EE we're using. We reference the TMPL class appropriately based on the version
		// This way we only have a few lines of code to make the plugin work with both version of EE easily.
		if (version_compare(APP_VER, '2', '<'))
		{
			global $TMPL;
			$this->TMPL =& $TMPL;
			
		} else {
			$this->EE =& get_instance();
			$this->TMPL =& $this->EE->TMPL;
		}
		
		
		// Set our variables based on passed parameters and defaults
		$this->class = ($this->TMPL->fetch_param('class')) ? $this->TMPL->fetch_param('class') : $this->class ;
		$this->count = ($this->TMPL->fetch_param('count')) ? $this->TMPL->fetch_param('count') : $this->count ;
		$this->via = ($this->TMPL->fetch_param('via')) ? $this->TMPL->fetch_param('via') : $this->via ;
		$this->related = ($this->TMPL->fetch_param('related')) ? $this->TMPL->fetch_param('related') : $this->related ;
		$this->related_desc = ($this->TMPL->fetch_param('related_description')) ? $this->TMPL->fetch_param('related_description') : $this->related_desc ;
		$this->tweet_text = ($this->TMPL->fetch_param('tweet_text')) ? $this->TMPL->fetch_param('tweet_text') : $this->tweet_text ;
		$this->url = ($this->TMPL->fetch_param('url')) ? $this->TMPL->fetch_param('url') : $this->url ;
		$this->lang = ($this->TMPL->fetch_param('lang')) ? $this->TMPL->fetch_param('lang') : $this->lang ;
		$this->exclude_js = ($this->TMPL->fetch_param('exclude_js')) ? $this->TMPL->fetch_param('exclude_js') : $this->exclude_js ;
		$this->wrap_a = ($this->TMPL->fetch_param('wrap_a')) ? $this->TMPL->fetch_param('wrap_a') : $this->wrap_a ;
		$this->anchor_text = ($this->TMPL->fetch_param('anchor_text')) ? $this->TMPL->fetch_param('anchor_text') : $this->anchor_text ;
		
		// Begin to build our HTML by opening our anchor tag
		$this->html .= '<a href="http://twitter.com/share"';
		
		// Add our anchor CSS class
		$this->html .= ' class="'.$this->class.'"';
		
		
		// The following attributes are added to the anchor tag if parameters are supplied
		
			// The "count" position (vertical, horizontal or none. horizontal if not supplied)
			$this->html .= ($this->count) ? ' data-count="'.$this->count.'"' : '' ;
			// "Via" twitter username
			$this->html .= ($this->via) ? ' data-via="'.ltrim($this->via,'@').'"' : '' ;
			// "Related" twitter account
			$this->html .= ($this->related) ? ' data-related="'.$this->related.'"' : '' ;
			// "Related" description
			$this->html = ($this->related && $this->related_desc) ? rtrim($this->html,'"').':'.$this->related_desc.'"' : $this->html ;
			// Custom "tweet text"
			$this->html .= ($this->tweet_text) ? ' data-text="'.$this->tweet_text.'"' : '' ;
			// Custom URL addition
			$this->html .= ($this->url) ? ' data-url="'.$this->url.'"' : '' ;
			// Add a language
			$this->html .= ($this->lang) ? ' data-lang="'.$this->lang.'"' : '' ;
			
		// We're now done with our potential anchor attributes
			
		
		// End our open anchor tag
		$this->html .= '>';
		
		// Add the linked text
		$this->html .= $this->anchor_text;
		
		// Close our anchor tag
		$this->html .='</a>';
		
		// Now we check to see if the anchor tag should be wrapped in any other tag
		if ($this->wrap_a)
		{
			// We want the use to be able to give their wrapping tag parameters
			// so we make an open tag and a close tag. The close tag will check for 
			// a "space" character in the string and if it exists we make sure the
			// tag attributes aren't included in the closing tag. Example:
			// {exp:tweet_button wrap_a="div class='my_button'"}
			$open_tag = $this->wrap_a;
			$close_tag = (strpos($this->wrap_a, ' ')) ? substr($this->wrap_a, 0, strpos($this->wrap_a, ' ')) : $this->wrap_a ;
			$this->html = "<".$open_tag.">\n\t".$this->html."\n</".$close_tag.">";
		}
		
		// Add the twitter widget JS reference unless it's set to be excluded
		$this->html .= ($this->exclude_js == 'y') ? '' : "\n".$this->js ;
		
		
		// We're done building the HTML so we assign it to $this->return_data which EE will use from our plugin
		return $this->return_data = $this->html;
		
	}
	
	
	
	/**
	 * Add javascript for Tweet Button
	 * This is for those who prefer to add the JS separately.
	 * It's useful if multiple tweet buttons are on a single page
	 *
	 * @return		string
	 */
	function js()
	{
		return $this->js;
	}
	
	
	
	
	/**
	 * Plugin Usage
	 */
	
	// This function describes how the plugin is used.
	//  Make sure and use output buffering
	
	function usage()
	{
		ob_start(); 
?>

Docs will be available after official release. Until then please review the documentation on GitHub available here:

http://github.com/focuslab/tweet_button.ee_addon

<?php
		$buffer         = ob_get_contents();
		
		ob_end_clean(); 
		
		return $buffer;
	}
	// End usage()

}


/* End of file pi.tweet_button.php */
/* Location for EE1.x: ./system/plugins/pi.er_tweet_button.php */
/* Location for EE2.x: ./system/expressionengine/third_party/tweet_button/pi.tweet_button.php */