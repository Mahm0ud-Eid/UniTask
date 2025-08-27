const element1 = document.getElementById("my-element1");
const element2 = document.getElementById("my-element2");
const element = document.getElementById("my-element");
const element3 = document.getElementById("my-element3");

let lastScrollY = window.scrollY;
let isScrolling = false;

const handleScroll = () => {
  isScrolling = true;
};

const updateElements = () => {
  if (isScrolling) {
    isScrolling = false;
    const currentScrollY = window.scrollY;
    if (currentScrollY > lastScrollY) {
      // Scrolling down
      element1.style.opacity = 1;
      element2.style.opacity = 1;
      // use animate__fadeInRight but remove animate__fadeOutRight
      element1.classList.remove("animate__fadeOutTopLeft");
      element2.classList.remove("animate__fadeOutTopRight");
      element1.classList.add("animate__animated", "animate__fadeInBottomRight");
      element2.classList.add("animate__animated", "animate__fadeInBottomLeft");
      element.style.transform = "translate(50%, 80%)";
      element3.style.transform = "translate(-50%, 80%)";

    } else {
      // Scrolling up
        // use animate__fadeOutRight
        element1.classList.add("animate__animated", "animate__fadeOutTopLeft");
        element2.classList.add("animate__animated", "animate__fadeOutTopRight");
      element1.style.opacity = 0;
      element2.style.opacity = 0;
      element.style.transform = "translateX(0)";
      element3.style.transform = "translateX(0)";
    }
    lastScrollY = currentScrollY;
  }
  requestAnimationFrame(updateElements);
};

updateElements();

window.addEventListener("scroll", handleScroll, { passive: true });
