<?php
/*
Plugin Name: Fulcrum Home Slider
Plugin URI: https://www.example.com/
Description: A custom slider plugin for WordPress
Version: 1.7
Author: Lucas Moura and Agustin Morcillo
Author URI: https://www.example.com/
*/

function fulcrum_home_slider_shortcode() {
    ob_start();
    ?>
  
  <div id="slider">
      <input
        type="radio"
        name="slider"
        class="slider-input"
        id="slide1"
        checked
      />
      <input type="radio" name="slider" class="slider-input" id="slide2" />
      <input type="radio" name="slider" class="slider-input" id="slide3" />

      <div id="slides">
        <div id="overflow">
          <div class="inner">
            <div class="slide slide_1">
              <div class="slide-content">
                <h1 class="slide_title slide_title-1">
                  Accelerating <br />
                  Business Models <br />
                  <span class="slide_title_sub"
                    >Through the Power of Digital</span
                  >
                </h1>
                <p class="slide_paragraph slide_paragraph-1">
                  We help companies navigate their digital future with people,
                  process, and technology.
                </p>
                <a href="#contactus-col" class="slide_btn slide_btn-1">
                  Let's Talk Digital
                </a>
              </div>
            </div>
            <div class="slide slide_2">
              <div class="slide-content">
                <img
                  src="/wp-content/uploads/2024/04/fd-ryze-logo.svg"
                  alt="FD Ryze logo"
                  class="slider_icon"
                />
                <h2 class="slide_title slide_title-2 big-title">
                  Elevating Businesses with Artificial Intelligence
                </h2>
                <p class="slide_paragraph slide_paragraph-2 big-paragraph">
                  Whether business or creative endeavors, Ryze is designed for
                  businesses to harness AI in unprecedented ways.
                </p>
                <a href="/platform/ryze/" class="slide_btn slide_btn-2"
                  >Learn More</a
                >
              </div>
            </div>
            <div class="slide slide_3">
              <div class="slide-content">
                <h2 class="slide_title slide_title-3 big-title">
                  Meet Our Team at <br />
                  B2B Online 2024
                  <span class="slide_title_sub"
                    >Booth #505 - Chicago, May 6-8</span
                  >
                </h2>
                <p class="slide_paragraph slide_paragraph-3 big-paragraph">
                  The World’s Leading eCommerce & Digital Marketing Conference
                  for Manufacturers & Distributors
                </p>
                <a
                  href="https://redstage.com/"
                  target="_blank"
                  class="slide_btn slide_btn-3"
                  >Explore Offerings</a
                >
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="controls">
        <label for="slide1"> </label>
        <label for="slide2"> </label>
        <label for="slide3"> </label>
      </div>
      <div id="bullets">
        <label for="slide1">
          <div class="loading-bar"></div>
        </label>
        <label for="slide2">
          <div class="loading-bar"></div>
        </label>
        <label for="slide3">
          <div class="loading-bar"></div>
        </label>
      </div>
    </div>
  </body>
</html>

    <?php
    return ob_get_clean();
}
add_shortcode('fulcrum-home-slider', 'fulcrum_home_slider_shortcode');

function fulcrum_home_slider_enqueue_scripts() {
    wp_enqueue_style('fulcrum-home-slider-css', plugin_dir_url(__FILE__) . 'slider.css');
    wp_enqueue_script('fulcrum-home-slider-js', plugin_dir_url(__FILE__) . 'script.js', array(), '1.0', true);
}
add_action('wp_enqueue_scripts', 'fulcrum_home_slider_enqueue_scripts');
?>