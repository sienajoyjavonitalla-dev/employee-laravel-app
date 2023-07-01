<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> 
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<script src="/js/signaturePad.js"></script>

<style>
    .kbw-signature { 
    width: 100%; height: 180px;
    }

    #signaturePad canvas{
        border: 1px solid #000;
        width: 100% !important;
        height: auto;
    }
</style>

<script type="text/javascript">

    var signaturePad = $('#signaturePad').signature({syncField: '#signature64', syncFormat: 'PNG'});

    $('#clear').click(function(e) {
        e.preventDefault();
        signaturePad.signature('clear');
        $("#signature64").val('');
    });
    
</script>
