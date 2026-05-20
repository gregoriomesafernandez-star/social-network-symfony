document.addEventListener('DOMContentLoaded', function () {
    
    const ias = new InfiniteAjaxScroll('.box-content', {
        item: '.publication-item',
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
        
        const message = document.createElement('div');
        message.className = 'text-center my-5 text-muted';
        message.innerHTML = `
            <p class="fs-6">No hay publicaciones para mostrar</p>
        `;

        document.querySelector('.box-content').appendChild(message);
    });

    ias.on('ready', function(event) {
        buttons();        
    });

    ias.on('rendered', function(event) {
        buttons();       
    });
});

function buttons() {
    
    $('[data-toggle="tooltip"]').tooltip()

    //BOTON IMAGE
    $(document).off('click', '.btn-image').on('click', '.btn-image', function() {
        $(this).parent().find('.pub-image').fadeToggle();
    });

    //BOTON ELIMINAR
    $(document).off('click', '.btn-delete-pub').on('click', '.btn-delete-pub', function() {

        $(this).closest('.publication-item').addClass('d-none');

        $.ajax({
            url: BASE_URL + '/publication/remove/'+$(this).attr("data-id"),
            type: 'GET',
            success: function(response){
                console.log(response);
            }
        });

    });

    //BOTON LIKE
    $(document).off('click', '.btn-like').on('click', '.btn-like', function() {

        $(this).addClass("d-none");
        $(this).parent().find(".btn-unlike").removeClass("d-none");

        $.ajax({
            url: BASE_URL + '/like/'+$(this).attr("data-id"),
            type: 'GET',
            success: function(response){
                console.log(response);
            }
        });

    });

    //BOTON UNLIKE
    $(document).off('click', '.btn-unlike').on('click', '.btn-unlike', function() {

        $(this).addClass("d-none");
        $(this).parent().find(".btn-like").removeClass("d-none");

        $.ajax({
            url: BASE_URL + '/unlike/'+$(this).attr("data-id"),
            type: 'GET',
            success: function(response){
                console.log(response);
            }
        });

    });
}