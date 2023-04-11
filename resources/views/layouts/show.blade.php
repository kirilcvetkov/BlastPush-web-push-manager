
<div class="modal fade" id="show" tabindex="-1" role="dialog" aria-labelledby="modalShow" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document" style="width: fit-content">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="modalShow"></h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
    </div>
  </div>
</div>

{{-- @section('content.js') --}}

<script type="text/javascript">
  $( document ).ready(function() {
    $( '#show' ).on( 'show.bs.modal', function (event) {
      var button = $( event.relatedTarget ); // Button that triggered the modal
      var route = button.data( 'route' ); // Extract info from data-* attributes
      var modal = $( this );

      modal.find( '.modal-title' ).text(button.data( 'title' ));
      modal.find( '.modal-body' ).text( "..." );

      $.ajax({
        url: route,
        type: 'GET',
        dataType: 'json',
        success: function ( data ) {
          var html = '';

          $.each(data, function( index, value ) {
            html += "<th scope='row'>" + index + "</th><td class='text-wrap'>" +
              (typeof value == 'object' ? JSON.stringify(value) : value) +
              "</td></tr>\n";
          });

          modal.find( '.modal-body' ).html(
            "<div class='table-responsive'><table class='table table-hover table-sm'>" + html + "</table></div>"
          );
        },
        error: function ( error ) {
          console.error({ error });
          alert('Error: ' + error)
        }
      });
    });
  });
</script>
@endsection
