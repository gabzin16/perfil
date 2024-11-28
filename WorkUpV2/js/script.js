// Script for adding a slight delay to button hover effect (optional)
const button = document.querySelector(".button-clique");

button.addEventListener("mouseover", () => {
  button.style.backgroundColor = "#F3EBCF";
});

button.addEventListener("mouseout", () => {
  button.style.backgroundColor = "#F7EEE9";
});