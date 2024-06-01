<?php
/*
Plugin Name: Smart Slider
Description: A custom slider plugin for WordPress
Version: 1.7
Author: Lucas Moura
*/

function lm_home_slider_shortcode() {
    ob_start();
    ?>
  <div id="slider">
  <input type="radio" name="slider" class="slider-input" id="slide1" checked />
  <input type="radio" name="slider" class="slider-input" id="slide2" />
  <input type="radio" name="slider" class="slider-input" id="slide3" />

  <div id="slides">
    <div id="overflow">
      <div class="inner">
        <div class="slide slide_1">
          <div class="slide-content">
            <h1 class="slide_title slide_title-1">
              Transforming <br />
              Your Ideas <br />
              <span class="slide_title_sub">Into Reality</span>
            </h1>
            <p class="slide_paragraph slide_paragraph-1">
              Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero. Sed cursus ante dapibus diam.
            </p>
            <a href="#contactus-col" class="slide_btn slide_btn-1">
              Get Started
            </a>
          </div>
        </div>
        <div class="slide slide_2">
          <div class="slide-content">
            <img
              src="https://via.placeholder.com/150"
              alt="Placeholder Image"
              class="slider_icon"
            />
            <h2 class="slide_title slide_title-2 big-title">
              Innovation and Excellence
            </h2>
            <p class="slide_paragraph slide_paragraph-2 big-paragraph">
              Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed nisi. Nulla quis sem at nibh elementum imperdiet.
            </p>
            <a href="#learn-more" class="slide_btn slide_btn-2">Learn More</a>
          </div>
        </div>
        <div class="slide slide_3">
          <div class="slide-content">
            <h2 class="slide_title slide_title-3 big-title">
              Join Us at <br />
              Future Tech Expo 2024
              <span class="slide_title_sub">Booth #101 - June 10-12</span>
            </h2>
            <p class="slide_paragraph slide_paragraph-3 big-paragraph">
              Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero. Sed cursus ante dapibus diam.
            </p>
            <a
              href="https://example.com/"
              target="_blank"
              class="slide_btn slide_btn-3"
              >Discover More</a
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


    <?php
    return ob_get_clean();
}
add_shortcode('lm-home-slider', 'lm_home_slider_shortcode');

function lm_home_slider_enqueue_scripts() {
    wp_enqueue_style('lm-home-slider-css', plugin_dir_url(__FILE__) . 'slider.css');
    wp_enqueue_script('lm-home-slider-js', plugin_dir_url(__FILE__) . 'script.js', array(), '1.0', true);
}
add_action('wp_enqueue_scripts', 'lm_home_slider_enqueue_scripts');
?>