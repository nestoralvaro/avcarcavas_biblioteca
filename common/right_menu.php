<div id="right_menu">
<?php
// Start counting time for the page load
$starttime = explode(' ', microtime());
$starttime = $starttime[1] + $starttime[0];

// Include SimplePie
// Located in the parent directory
include_once('./feeds/simplepie.inc');
include_once('./feeds/idna_convert.class.php');

// Create a new instance of the SimplePie object
$feed = new SimplePie();

$av_feed= "http://avcarcavas.wordpress.com/feed/";
// Strip slashes if magic quotes is enabled (which automatically escapes certain characters)
if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
{
	$av_feed = stripslashes($av_feed);
}
// Use the URL that was passed to the page in SimplePie
$feed->set_feed_url($av_feed);

// Initialize the whole SimplePie object.  Read the feed, process it, parse it, cache it, and 
// all that other good stuff.  The feed's information will not be available to SimplePie before 
// this is called.
$success = $feed->init();

// We'll make sure that the right content type and character encoding gets set automatically.
// This function will grab the proper character encoding, as well as set the content type to text/html.
$feed->handle_content_type();

// When we end our PHP block, we want to make sure our DOCTYPE is on the top line to make 
// sure that the browser snaps into Standards Mode.
?>

<div id="site">

	<div id="feed_contents">
        <?php
            // I don't want to display errors
            if (1== 2) {
        ?>
		<div class="chunk">
			<?php
			// Check to see if there are more than zero errors (i.e. if there are any errors at all)
			if ($feed->error())
			{
				// If so, start a <div> element with a classname so we can style it.
				echo '<div class="sp_errors">' . "\r\n";

					// ... and display it.
					echo '<p>' . htmlspecialchars($feed->error()) . "</p>\r\n";

				// Close the <div> element we opened.
				echo '</div>' . "\r\n";
			}
			?>
		</div>
        <?php
            } // End of the "hiden" errors part
        ?>

		<div id="sp_results">

			<!-- As long as the feed has data to work with... -->
			<?php if ($success): ?>

				<!-- Let's begin looping through each individual news item in the feed. -->
				<?php foreach($feed->get_items() as $item): ?>
					<div class="chunk">

						<!-- If the item has a permalink back to the original post (which 99% of them do), link the item's title to it. -->
						<h4><?php if ($item->get_permalink()) echo '<a href="' . $item->get_permalink() . '" target="_blank">'; echo $item->get_title(); if ($item->get_permalink()) echo '</a>'; ?>&nbsp;<br/><?php echo $item->get_date('j M Y, g:i a'); ?></h4>

						<!-- Display the item's primary content. -->
						<?php 
                            // echo $item->get_content(); 
                        ?>

					</div>

				<!-- Stop looping through each item once we've gone through all of them. -->
				<?php endforeach; ?>

			<!-- From here on, we're no longer using data from the feed. -->
			<?php endif; ?>

		</div>

        <?php
            // I don't want to display this
            if (1== 2) {
        ?>
		<div>
			<!-- Display how fast the page was rendered. -->
			<p class="footnote">Page processed in <?php $mtime = explode(' ', microtime()); echo round($mtime[0] + $mtime[1] - $starttime, 3); ?> seconds.</p>
        <?php
            } // End of the "hiden" part
        ?>
			<!-- Display the version of SimplePie being loaded. -->
			<p class="footnote">Powered by <a href="<?php echo SIMPLEPIE_URL; ?>"><?php echo SIMPLEPIE_NAME . ' ' . SIMPLEPIE_VERSION . ', Build ' . SIMPLEPIE_BUILD; ?></a>. SimplePie is &copy; 2004&ndash;<?php echo date('Y'); ?>, Ryan Parman and Geoffrey Sneddon, and licensed under the <a href="http://www.opensource.org/licenses/bsd-license.php">BSD License</a>.</p>
		</div>

	</div>

</div>

</div><!-- FIN right_menu -->
