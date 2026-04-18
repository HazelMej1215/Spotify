const audio = document.getElementById('audio-player');
const btnPlayPause = document.getElementById('btn-play-pause');
const repNombre = document.getElementById('rep-nombre');
const repAutor = document.getElementById('rep-autor');
const repImagen = document.getElementById('rep-imagen');
const barraProgreso = document.getElementById('barra-progreso');
const tiempoActual = document.getElementById('tiempo-actual');
const tiempoTotal = document.getElementById('tiempo-total');

let filas = Array.from(document.querySelectorAll('.fila-playlist'));
let indiceActual = -1;
let colaIA = [];
let modoIA = false;

// =====================
// ALGORITMO IA
// =====================
function generarColaIA(generoActual, idActual) {
    // Busca canciones del mismo genero excluyendo la actual
    let similares = filas.filter(f => 
        f.dataset.genero === generoActual && 
        f.dataset.id !== idActual
    );

    // Mezcla aleatoriamente las similares
    similares = similares.sort(() => Math.random() - 0.5);

    // Canciones de otros generos al final
    let otros = filas.filter(f => 
        f.dataset.genero !== generoActual && 
        f.dataset.id !== idActual
    );
    otros = otros.sort(() => Math.random() - 0.5);

    colaIA = [...similares, ...otros];
    modoIA = true;

    mostrarNotificacionIA(similares.length);
}

function mostrarNotificacionIA(cantidad) {
    let notif = document.getElementById('notif-ia');
    if (!notif) {
        notif = document.createElement('div');
        notif.id = 'notif-ia';
        notif.style.cssText = `
            position: fixed;
            bottom: 100px;
            right: 24px;
            background: #1db954;
            color: #000;
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 700;
            z-index: 9999;
            transition: opacity 0.5s;
        `;
        document.body.appendChild(notif);
    }
    notif.style.opacity = '1';
    notif.textContent = 'IA: ' + cantidad + ' canciones similares en cola';
    setTimeout(() => { notif.style.opacity = '0'; }, 3000);
}

// =====================
// REPRODUCTOR
// =====================
function cargarCancion(indice) {
    if (indice < 0 || indice >= filas.length) return;

    filas.forEach(f => f.classList.remove('activa'));
    filas[indice].classList.add('activa');
    indiceActual = indice;

    const fila   = filas[indice];
    const mp3    = fila.dataset.mp3;
    const nombre = fila.dataset.nombre;
    const autor  = fila.dataset.autor;
    const imagen = fila.dataset.imagen;
    const genero = fila.dataset.genero;
    const id     = fila.dataset.id;

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

    // Activar IA en la primera cancion
    generarColaIA(genero, id);
}

function cargarCancionIA(fila) {
    filas.forEach(f => f.classList.remove('activa'));
    fila.classList.add('activa');
    indiceActual = filas.indexOf(fila);

    audio.src    = fila.dataset.mp3;
    repNombre.textContent = fila.dataset.nombre;
    repAutor.textContent  = fila.dataset.autor;

    if (fila.dataset.imagen) {
        repImagen.src = fila.dataset.imagen;
        repImagen.style.display = 'block';
    } else {
        repImagen.style.display = 'none';
    }

    barraProgreso.value = 0;
    tiempoActual.textContent = '0:00';

    generarColaIA(fila.dataset.genero, fila.dataset.id);
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
        const siguienteFila = colaIA.shift();
        cargarCancionIA(siguienteFila);
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

// Click en fila
filas.forEach((fila, indice) => {
    fila.addEventListener('click', (e) => {
        if (e.target.classList.contains('btn-play')) return;
        cargarCancion(indice);
        audio.play();
        btnPlayPause.textContent = '⏸';
    });
});