const radioBtns = document.querySelectorAll(".slider-input")
const loadingBars = document.querySelectorAll(".loading-bar")
const SLIDER__TIMER = 90
let currentIndex = 0
let timer

// Function to switch to the next radio button
function switchToNextRadio() {
  // Uncheck the current radio button
  radioBtns[currentIndex].checked = false

  // Increment the index or reset to 0 if it exceeds the length
  currentIndex = (currentIndex + 1) % radioBtns.length

  // Check the next radio button
  radioBtns[currentIndex].checked = true

  // Reset progress and start animation only if the slide is active
  if (radioBtns[currentIndex].checked) {
    resetProgress()
    animateProgressBar()
  }
}

// Function to animate progress bar
function animateProgressBar() {
  let progress = 0
  clearInterval(timer)

  // Start the animation
  timer = setInterval(() => {
    progress += 1

    // Update the width of the loading bar
    loadingBars[currentIndex].style.width = `${progress}%`

    // Check if progress reaches 100%
    if (progress >= 100) {
      clearInterval(timer)
      switchToNextRadio() // Switch to the next radio button once the progress is complete
    }
  }, SLIDER__TIMER)
}

// Function to reset the progress of all loading bars
function resetProgress() {
  loadingBars.forEach((bar) => {
    bar.style.width = "0"
  })
}

// Start the timer
animateProgressBar()

// Add click event listener to each radio button to manually switch and reset the timer
radioBtns.forEach((radioBtn, index) => {
  radioBtn.addEventListener("click", () => {
    currentIndex = index
    resetProgress() // Reset the progress of all loading bars
    clearInterval(timer) // Stop the current timer
    animateProgressBar() // Start a new progress animation
  })
})