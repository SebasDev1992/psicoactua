/**
 * Gestiona la búsqueda de psicólogos sin recargar la página.
 *
 * Filtra por nombre y especialidad, ignorando mayúsculas
 * y tildes para facilitar la búsqueda.
 */
document.addEventListener('DOMContentLoaded', () => {
    const buscador = document.querySelector('#buscarPsicologo');
    const tarjetas = document.querySelectorAll('[data-psychologist-card]');
    const contador = document.querySelector('#contadorResultados');
    const sinResultados = document.querySelector('#sinResultados');

    // La vista puede mostrarse sin buscador si no hay profesionales activos.
    if (!buscador || tarjetas.length === 0) {
        return;
    }

    // Normaliza el texto para que "psicologia" también encuentre "Psicología".
    const normalizar = (texto) => texto
        .toLocaleLowerCase('es-CO')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '');

    const filtrarProfesionales = () => {
        const termino = normalizar(buscador.value.trim());
        let visibles = 0;

        tarjetas.forEach((tarjeta) => {
            const contenido = normalizar(tarjeta.dataset.search || '');
            const coincide = contenido.includes(termino);

            tarjeta.classList.toggle('hidden', !coincide);

            if (coincide) {
                visibles++;
            }
        });

        // Informa el resultado para usuarios y tecnologías de asistencia.
        if (contador) {
            contador.textContent = `Mostrando ${visibles} de ${tarjetas.length} profesionales`;
        }

        if (sinResultados) {
            sinResultados.classList.toggle('hidden', visibles > 0);
        }
    };

    buscador.addEventListener('input', filtrarProfesionales);
});
