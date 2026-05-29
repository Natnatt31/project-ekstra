// Particles.js Configuration — Gaming Theme
particlesJS('particles-js', {
  particles: {
    number: {
      value: 80,
      density: { enable: true, value_area: 900 }
    },
    color: { value: ['#00D4FF', '#FF6B00', '#FFFFFF'] },
    shape: {
      type: ['circle', 'triangle'],
      stroke: { width: 0, color: '#000000' }
    },
    opacity: {
      value: 0.4,
      random: true,
      anim: { enable: true, speed: 0.8, opacity_min: 0.1, sync: false }
    },
    size: {
      value: 3,
      random: true,
      anim: { enable: true, speed: 2, size_min: 0.5, sync: false }
    },
    line_linked: {
      enable: true,
      distance: 150,
      color: '#00D4FF',
      opacity: 0.15,
      width: 1
    },
    move: {
      enable: true,
      speed: 1.5,
      direction: 'none',
      random: true,
      straight: false,
      out_mode: 'out',
      bounce: false,
      attract: { enable: true, rotateX: 600, rotateY: 1200 }
    }
  },
  interactivity: {
    detect_on: 'canvas',
    events: {
      onhover: { enable: true, mode: 'grab' },
      onclick: { enable: true, mode: 'push' },
      resize: true
    },
    modes: {
      grab: { distance: 180, line_linked: { opacity: 0.4 } },
      push: { particles_nb: 3 }
    }
  },
  retina_detect: true
});
