/*----------  Configuración de formularios AJAX  ----------*/
const formularios_ajax = document.querySelectorAll(".FormularioAjax");

formularios_ajax.forEach(formulario => {
    formulario.addEventListener("submit", function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción se ejecutará inmediatamente",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                enviar_formulario_ajax(formulario);
            }
        });
    });
});

/*----------  Función para enviar formularios AJAX  ----------*/
function enviar_formulario_ajax(formulario) {
    let data = new FormData(formulario);
    let method = formulario.getAttribute("method");
    let action = formulario.getAttribute("action");

    let encabezados = new Headers();

    let config = {
        method: method,
        headers: encabezados,
        mode: 'cors',
        cache: 'no-cache',
        body: data
    };

    // Mostrar loading
    Swal.fire({
        title: 'Procesando...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch(action, config)
        .then(respuesta => respuesta.json())
        .then(respuesta => {
            return alertas_ajax(respuesta);
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error en la comunicación con el servidor',
                confirmButtonText: 'Aceptar'
            });
            console.error('Error:', error);
        });
}

/*----------  Función para mostrar alertas  ----------*/
function alertas_ajax(alerta) {
    if (alerta.tipo == "simple") {
        Swal.fire({
            icon: alerta.icono,
            title: alerta.titulo,
            text: alerta.texto,
            confirmButtonText: 'Aceptar'
        });
    } else if (alerta.tipo == "recargar") {
        Swal.fire({
            icon: alerta.icono,
            title: alerta.titulo,
            text: alerta.texto,
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if (result.isConfirmed) {
                location.reload();
            }
        });
    } else if (alerta.tipo == "limpiar") {
        Swal.fire({
            icon: alerta.icono,
            title: alerta.titulo,
            text: alerta.texto,
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.querySelectorAll(".FormularioAjax")[0].reset();
            }
        });
    } else if (alerta.tipo == "redireccionar") {
        Swal.fire({
            icon: alerta.icono,
            title: alerta.titulo,
            text: alerta.texto,
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = alerta.url;
            }
        });
    }
}

/*----------  Enviar datos de formulario con DataTables  ----------*/
function enviar_formulario_datatable(formulario) {
    let data = new FormData(formulario);
    let method = formulario.getAttribute("method");
    let action = formulario.getAttribute("action");

    let encabezados = new Headers();

    let config = {
        method: method,
        headers: encabezados,
        mode: 'cors',
        cache: 'no-cache',
        body: data
    };

    fetch(action, config)
        .then(respuesta => respuesta.json())
        .then(respuesta => {
            return alertas_ajax(respuesta);
        });
}

/*----------  Confirmar eliminación  ----------*/
document.addEventListener('DOMContentLoaded', function() {
    // Manejar todos los formularios de eliminación
    const formulariosEliminar = document.querySelectorAll('.FormularioAjax');
    
    formulariosEliminar.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Verificar si es un formulario de eliminación
            const moduloInput = form.querySelector('[name*="modulo"]');
            if (moduloInput && moduloInput.value === 'eliminar') {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡No podrás revertir esta acción!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        enviar_formulario_ajax(form);
                    }
                });
            } else {
                enviar_formulario_ajax(form);
            }
        });
    });
});

/*----------  Cerrar alertas automáticamente  ----------*/
function alerta_toast(icono, titulo, tiempo = 3000) {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: tiempo,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    Toast.fire({
        icon: icono,
        title: titulo
    });
}