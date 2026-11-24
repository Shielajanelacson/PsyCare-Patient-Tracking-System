<div class="cat-layer" id="catLayer" aria-hidden="true"></div>

<script>
(function () {
  const layer = document.getElementById('catLayer');
  const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // one entry per cat: fur colours + walking speed (px per second)
  const CATS = [
    { fur: '#e3a05a', dark: '#b87a3a', belly: '#f6dfc0', speed: 38 },
    { fur: '#9aa6b3', dark: '#6f7b88', belly: '#dfe5ea', speed: 30 },
    { fur: '#4a4a57', dark: '#2d2d37', belly: '#7b7b89', speed: 46 }
  ];
  const messages = [
    "Take a slow, deep breath. In... and out.",
    "You are doing great, Doctor!",
    "Every patient's story matters.",
    "Don't forget to drink some water.",
    "Rest is part of the work too.",
    "Small progress is still progress.",
    "Purr... meow!"
  ];
  let msgIndex = 0;

  const CAT_W = 64;

  function catSvg() {
    return '' +
    '<svg viewBox="0 0 80 56" xmlns="http://www.w3.org/2000/svg">' +
      '<g class="c-tail"><path d="M16 28 C3 28 0 14 8 8" fill="none" stroke="var(--fur)" stroke-width="5.5" stroke-linecap="round"/></g>' +
      '<g class="c-leg l-far-b"><rect x="23" y="34" width="6" height="17" rx="3" fill="var(--dark)"/></g>' +
      '<g class="c-leg l-far-f"><rect x="51" y="34" width="6" height="17" rx="3" fill="var(--dark)"/></g>' +
      '<g class="c-body">' +
        '<ellipse cx="37" cy="28" rx="25" ry="12.5" fill="var(--fur)"/>' +
        '<ellipse cx="38" cy="34" rx="16" ry="6" fill="var(--belly)" opacity=".55"/>' +
        '<path d="M28 18 L30 25 M37 17 L38 25 M46 18 L45 25" stroke="var(--dark)" stroke-width="2.4" stroke-linecap="round" opacity=".6"/>' +
      '</g>' +
      '<g class="c-leg l-near-b"><rect x="17" y="34" width="6.5" height="17" rx="3.2" fill="var(--fur)"/><ellipse cx="20" cy="51" rx="4.6" ry="2.4" fill="var(--belly)"/></g>' +
      '<g class="c-leg l-near-f"><rect x="46" y="34" width="6.5" height="17" rx="3.2" fill="var(--fur)"/><ellipse cx="49" cy="51" rx="4.6" ry="2.4" fill="var(--belly)"/></g>' +
      '<g class="c-head">' +
        '<path d="M53 14 L55 2 L63 10 Z" fill="var(--fur)"/><path d="M65 10 L72 3 L73 15 Z" fill="var(--fur)"/>' +
        '<path d="M56 11 L56 6 L60 10 Z" fill="#f2a5b0"/><path d="M68 10 L71 7 L71 12 Z" fill="#f2a5b0"/>' +
        '<ellipse cx="64" cy="21" rx="12" ry="10.5" fill="var(--fur)"/>' +
        '<g class="c-eye"><ellipse cx="68" cy="19" rx="2.6" ry="3.2" fill="#fff"/><ellipse cx="68.8" cy="19.4" rx="1.5" ry="2.2" fill="#2b2118"/></g>' +
        '<ellipse cx="74.6" cy="23" rx="1.6" ry="1.2" fill="#e5788a"/>' +
        '<path d="M74 25 C73 27 71 27 70 26" fill="none" stroke="#5a3a22" stroke-width="1" stroke-linecap="round"/>' +
        '<path d="M72 22 L80 20 M72 24 L80 25" stroke="#5a3a22" stroke-width=".8" stroke-linecap="round" opacity=".7"/>' +
      '</g>' +
    '</svg>';
  }

  const cats = CATS.map(function (c, i) {
    const el = document.createElement('div');
    el.className = 'wcat';
    el.style.setProperty('--fur', c.fur);
    el.style.setProperty('--dark', c.dark);
    el.style.setProperty('--belly', c.belly);
    el.innerHTML = '<div class="wcat-bubble"></div><div class="wcat-flip">' + catSvg() + '</div>';
    layer.appendChild(el);
    const cat = {
      el: el,
      flip: el.querySelector('.wcat-flip'),
      bubble: el.querySelector('.wcat-bubble'),
      x: 40 + Math.random() * Math.max(100, window.innerWidth - 160),
      dir: Math.random() < 0.5 ? -1 : 1,
      speed: c.speed,
      state: 'walk',
      until: 0,
      timer: null
    };
    el.addEventListener('click', function () { poke(cat); });
    return cat;
  });

  function say(cat, message) {
    cat.bubble.textContent = message;
    const max = window.innerWidth - CAT_W;
    cat.el.classList.toggle('bub-left', cat.x < 110);
    cat.el.classList.toggle('bub-right', cat.x > max - 110);
    cat.bubble.classList.add('show');
    clearTimeout(cat.timer);
    cat.timer = setTimeout(function () { cat.bubble.classList.remove('show'); }, 4500);
  }

  function setState(cat, state, now) {
    cat.state = state;
    cat.el.classList.toggle('walking', state === 'walk');
    cat.el.classList.toggle('sitting', state === 'sit');
    if (state === 'walk') {
      cat.until = now + 4000 + Math.random() * 9000;
      if (Math.random() < 0.35) cat.dir *= -1;
    } else {
      cat.until = now + 1800 + Math.random() * 3800;
    }
  }

  function poke(cat) {
    say(cat, messages[msgIndex]);
    msgIndex = (msgIndex + 1) % messages.length;
    cat.el.classList.remove('jump');
    void cat.el.offsetWidth;
    cat.el.classList.add('jump');
    setState(cat, 'sit', performance.now());
    cat.until = performance.now() + 4500;
  }

  function place(cat) {
    cat.el.style.transform = 'translateX(' + cat.x.toFixed(1) + 'px)';
    cat.flip.style.transform = cat.dir < 0 ? 'scaleX(-1)' : 'scaleX(1)';
  }

  const start = performance.now();
  cats.forEach(function (cat, i) {
    if (reduce) {
      cat.x = window.innerWidth - (i + 1) * (CAT_W + 14) - 10;
      cat.dir = -1;
      setState(cat, 'sit', start);
      cat.until = Infinity;
    } else {
      setState(cat, 'walk', start);
    }
    place(cat);
  });

  let last = start;
  function tick(now) {
    const dt = Math.min(0.05, (now - last) / 1000);
    last = now;
    const max = window.innerWidth - CAT_W;
    cats.forEach(function (cat) {
      if (cat.state === 'walk') {
        cat.x += cat.dir * cat.speed * dt;
        if (cat.x <= 0) { cat.x = 0; cat.dir = 1; }
        if (cat.x >= max) { cat.x = max; cat.dir = -1; }
      }
      if (now > cat.until) setState(cat, cat.state === 'walk' ? 'sit' : 'walk', now);
      place(cat);
    });
    requestAnimationFrame(tick);
  }
  if (!reduce) requestAnimationFrame(tick);

  window.addEventListener('resize', function () {
    const max = window.innerWidth - CAT_W;
    cats.forEach(function (cat) { cat.x = Math.max(0, Math.min(cat.x, max)); place(cat); });
  });

  try {
    if (!sessionStorage.getItem('catHello')) {
      sessionStorage.setItem('catHello', '1');
      setTimeout(function () { say(cats[0], "Meow! Need a mindful moment? Tap me."); }, 900);
    }
  } catch (e) {}
})();
</script>