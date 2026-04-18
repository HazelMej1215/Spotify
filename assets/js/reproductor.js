const audio = document.getElementById('audio-player');
const btnPlayPause = document.getElementById('btn-play-pause');
const repNombre = document.getElementById('rep-nombre');
const repAutor  = document.getElementById('rep-autor');
const repImagen = document.getElementById('rep-imagen');
const repPlaceholder = document.getElementById('rep-placeholder');
const barraProgreso  = document.getElementById('barra-progreso');
const tiempoActual   = document.getElementById('tiempo-actual');
const tiempoTotal    = document.getElementById('tiempo-total');

let filas = Array.from(document.querySelectorAll('.fila-playlist'));
let indiceActual = -1;
let colaIA = [];
let modoIA = false;

function mostrarReproductor() {
    const rep = document.getElementById('reproductor');
    if (rep) {
        rep.style.display = 'flex';
        rep.style.flexShrink = '0';
    }
}

function generarColaIA(generoActual, idActual) {
    let similares = filas.filter(f =>
        f.dataset.genero === generoActual && f.dataset.id !== idActual
    ).sort(() => Math.random() - 0.5);

    let otros = filas.filter(f =>
        f.dataset.genero !== generoActual && f.dataset.id !== idActual
    ).sort(() => Math.random() - 0.5);

    colaIA = [...similares, ...otros];
    modoIA = true;

    const notif = document.getElementById('notif-ia');
    if (notif) {
        notif.style.opacity = '1';
        notif.textContent = 'IA: ' + similares.length + ' canciones similares en cola';
        setTimeout(() => { notif.style.opacity = '0'; }, 3000);
    }
}

function cargarCancion(indice) {
    if (indice < 0 || indice >= filas.length) return;
    mostrarReproductor();

    filas.forEach(f => f.classList.remove('activa'));
    filas[indice].classList.add('activa');
    indiceActual = indice;

    const fila   = filas[indice];
    cargarDatos(fila);
    generarColaIA(fila.dataset.genero, fila.dataset.id);
}

function cargarCancionIA(fila) {
    mostrarReproductor();
    filas.forEach(f => f.classList.remove('activa'));
    fila.classList.add('activa');
    indiceActual = filas.indexOf(fila);
    cargarDatos(fila);
    generarColaIA(fila.dataset.genero, fila.dataset.id);
}

function cargarDatos(fila) {
    audio.src = fila.dataset.mp3;
    repNombre.textContent = fila.dataset.nombre;
    repAutor.textContent  = fila.dataset.autor;
    barraProgreso.value   = 0;
    tiempoActual.textContent = '0:00';

    if (fila.dataset.imagen) {
        repImagen.src = fila.dataset.imagen;
        repImagen.style.display = 'block';
        if (repPlaceholder) repPlaceholder.style.display = 'none';
    } else {
        repImagen.style.display = 'none';
        if (repPlaceholder) repPlaceholder.style.display = 'flex';
    }
}

function reproducir(btn) {
    const fila   = btn.closest('.fila-playlist');
    const indice = filas.indexOf(fila);
    cargarCancion(indice);
    audio.play();
    btnPlayPause.textContent = '⏸';
}

function togglePlay() {
    if (audio.paused) {
        audio.play();
        btnPlayPause.textContent = '⏸';
    } else {
        audio.pause();
        btnPlayPause.textContent = '▶';
    }
}

function siguiente() {
    if (modoIA && colaIA.length > 0) {
        const sig = colaIA.shift();
        cargarCancionIA(sig);
        audio.play();
        btnPlayPause.textContent = '⏸';
    } else {
        const sig = indiceActual + 1;
        if (sig < filas.length) {
            cargarCancion(sig);
            audio.play();
            btnPlayPause.textContent = '⏸';
        }
    }
}

function anterior() {
    const ant = indiceActual - 1;
    if (ant >= 0) {
        cargarCancion(ant);
        audio.play();
        btnPlayPause.textContent = '⏸';
    }
}

function cambiarTiempo(valor) {
    if (audio.duration) {
        audio.currentTime = (valor / 100) * audio.duration;
    }
}

function cambiarVolumen(valor) {
    audio.volume = valor / 100;
}

function formatearTiempo(seg) {
    const m = Math.floor(seg / 60);
    const s = Math.floor(seg % 60);
    return m + ':' + (s < 10 ? '0' : '') + s;
}

audio.addEventListener('timeupdate', () => {
    if (audio.duration) {
        barraProgreso.value = (audio.currentTime / audio.duration) * 100;
        tiempoActual.textContent = formatearTiempo(audio.currentTime);
        tiempoTotal.textContent  = formatearTiempo(audio.duration);
    }
});

audio.addEventListener('ended', () => siguiente());

filas.forEach((fila, indice) => {
    fila.addEventListener('click', (e) => {
        if (e.target.classList.contains('btn-play-fila')) return;
        cargarCancion(indice);
        audio.play();
        btnPlayPause.textContent = '⏸';
    });
});