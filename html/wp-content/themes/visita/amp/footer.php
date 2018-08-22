<footer class="amp-wp-footer">
	<div>
		<h2><?php echo esc_html( $this->get( 'blog_name' ) ); ?></h2>
		<a href="#top" class="back-to-top"><?php _e( 'Back to top', 'visita' ); ?></a>
	</div>
</footer>
<amp-analytics type="googleanalytics" id="analytics1">
	<script type="application/json">
	{
	  "vars": {
	    "account": "UA-12444378-16"
	  },
	  "triggers": {
	    "trackPageview": {
	      "on": "visible",
	      "request": "pageview"
	    },
			"trackEvent" : {
	      "on": "click",
	      "request": "event",
				"selector": ".sh-links > a",
	      "vars": {
	        "eventCategory": "Social",
	        "eventAction": "Shared",
					"eventLabel": "${name}"
	      }
	    }
	  }
	}
	</script>
</amp-analytics>
