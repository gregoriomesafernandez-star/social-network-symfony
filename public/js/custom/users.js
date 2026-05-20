document.addEventListener('DOMContentLoaded', function () {
    
    if (window.esBusqueda === true) {
            followButtons();
            return;   // No ejecuta el infinite scroll
    }
    
    const ias = new InfiniteAjaxScroll('.box-users', {
        item: '.user-item',
        next: '.pagination .page-item:last-child a, .pagination a[rel="next"]',
        pagination: '.pagination',

        spinner: {
            element: '.loading-spinner',
            show: () => document.querySelector('.loading-spinner').style.display = 'block',
            hide: () => document.querySelector('.loading-spinner').style.display = 'none'
        },

        last: {
            text: '',   
        }
    });

    // === Eventos del Infinite Ajax Scroll ===
    ias.on('last', function () {
        // Creamos el mensaje y lo insertamos después del último user-item
        const message = document.createElement('div');
        message.className = 'text-center my-5 text-muted';
        message.innerHTML = `
            <p class="fs-6">No hay usuarios para mostrar</p>
        `;

        // Insertamos el mensaje dentro del contenedor .box-users
        document.querySelector('.box-users').appendChild(message);
    });

    ias.on('ready', function(event) {
        followButtons();        // Primera vez que carga
    });

    ias.on('rendered', function(event) {
        followButtons();        // Cada vez que se cargan nuevos usuarios
    });
});

function followButtons() {
    
    $(document).off('click', '.btn-follow').on('click', '.btn-follow',function(){
        
        $(this).addClass("d-none");
        $(this).parent().find(".btn-unfollow").removeClass("d-none");

        $.ajax({
            url: BASE_URL + '/follow',
            type: 'POST',
            data: {
                followed: $(this).attr("data-followed")
            },
            success: function(response){
                console.log(response);
            }
        });
    });

    $(document).off('click', '.btn-unfollow').on('click', '.btn-unfollow',function(){
        
        $(this).addClass("d-none");
        $(this).parent().find(".btn-follow").removeClass("d-none");

        $.ajax({
            url: BASE_URL + '/unfollow',
            type: 'POST',
            data: {
                followed: $(this).attr("data-followed")
            },
            success: function(response){
                console.log(response);
            }
        });
    });
}