const audio = document.getElementById('audio-player');
const btnPlayPause = document.getElementById('btn-play-pause');
const repNombre = document.getElementById('rep-nombre');
const repAutor = document.getElementById('rep-autor');
const repImagen = document.getElementById('rep-imagen');
const barraProgreso = document.getElementById('barra-progreso');
const tiempoActual = document.getElementById('tiempo-actual');
const tiempoTotal = document.getElementById('tiempo-total');

let filas = document.querySelectorAll('.fila-playlist');
let indiceActual = -1;

function reproducir(btn) {
    const fila = btn.closest('.fila-playlist');
    const indice = Array.from(filas).indexOf(fila);
    cargarCancion(indice);
    audio.play();
    btnPlayPause.textContent = '⏸';
}

function cargarCancion(indice) {
    if (indice < 0 || indice >= filas.length) return;

    filas.forEach(f => f.classList.remove('activa'));
    filas[indice].classList.add('activa');
    indiceActual = indice;

    const fila = filas[indice];
    const mp3   = fila.dataset.mp3;
    const nombre = fila.dataset.nombre;
    const autor  = fila.dataset.autor;
    const imagen = fila.dataset.imagen;

    audio.src = mp3;
    repNombre.textContent = nombre;
    repAutor.textContent  = autor;

    if (imagen) {
        repImagen.src = imagen;
        repImagen.style.display = 'block';
    } else {
        repImagen.style.display = 'none';
    }

    barraProgreso.value = 0;
    tiempoActual.textContent = '0:00';
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
    const siguiente = indiceActual + 1;
    if (siguiente < filas.length) {
        cargarCancion(siguiente);
        audio.play();
        btnPlayPause.textContent = '⏸';
    }
}

function anterior() {
    const anterior = indiceActual - 1;
    if (anterior >= 0) {
        cargarCancion(anterior);
        audio.play();
        btnPlayPause.textContent = '⏸';
    }
}

function cambiarTiempo(valor) {
    if (audio.duration) {
        audio.currentTime = (valor / 100) * audio.duration;
    }
}

function formatearTiempo(segundos) {
    const min = Math.floor(segundos / 60);
    const seg = Math.floor(segundos % 60);
    return min + ':' + (seg < 10 ? '0' : '') + seg;
}

audio.addEventListener('timeupdate', () => {
    if (audio.duration) {
        barraProgreso.value = (audio.currentTime / audio.duration) * 100;
        tiempoActual.textContent = formatearTiempo(audio.currentTime);
        tiempoTotal.textContent  = formatearTiempo(audio.duration);
    }
});

audio.addEventListener('ended', () => {
    siguiente();
});

// Reproducir al hacer click en la fila
filas.forEach((fila, indice) => {
    fila.addEventListener('click', (e) => {
        if (e.target.classList.contains('btn-play')) return;
        cargarCancion(indice);
        audio.play();
        btnPlayPause.textContent = '⏸';
    });
});