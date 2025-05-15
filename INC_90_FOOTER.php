<script>
    $( window ).load(function() {
        var navheight=$("nav").height();
        //alert(navheight);
        $("#firstcontainer").css("margin-top",navheight-50);

        //http://bootsnipp.com/snippets/50me8
        $('#preloader').fadeOut('slow');
        $('body').css({'overflow':'visible'});

        $(".experiment_filter").change(function(){
            var query='';
            var filter1=$("#type_of_forcing").val();
            var filter2=$("#field_of_test").val();
            var filter3=$("#laboratory").val();
            var filter4=$("#infrastructure").val();
            var filter5=$("#installation").val();

            if (filter1!=null) {
                query+=" AND type_of_forcing IN ("+filter1+")";
            }
            if (filter2!=null) {
                query+=" AND field_of_test IN ("+filter2+")";
            }
            if (filter3!=null) {
                query+=" AND Laboratory IN ("+filter3+")";
            }
            if (filter4!=null) {
                query+=" AND infrastructure IN ("+filter4+")";
            }
            if (filter5!=null) {
                query+=" AND installation IN ("+filter5+")";
            }

            query = query.substring(5);

            query="SELECT tb_experiments.*,pcs_file.file as immagine,f2.file as allegato FROM tb_experiments LEFT JOIN pcs_file ON pcs_file.id_elem=tb_experiments.id AND pcs_file.tb='tb_experiments' AND pcs_file.tipo_file='immagine' LEFT JOIN pcs_file f2 ON f2.id_elem=tb_experiments.id AND f2.tb='tb_experiments' AND f2.tipo_file='allegato' WHERE "+query+" order by tb_experiments.title";

            var params={};
            params.query=query;
            console.log(query);
            $.ajax({
                        type: "POST",
                        url: "ajax_esegui_query.php",
                        data: params,
                        dataType: 'json',
                        success: function(data){
                            console.log(data);
                            if (data.result==true) {
                                $.notify(data.msg,'success');
                                $("#mydiv").html(data.html);
                                //setTimeout(function(){location.reload();}, 2000);
                            } else {
                                $.notify(data.error);
                            }
                        },
                        error: function(data) {
                            console.log(data);
                        }
                    });

            //console.log(query);


        });
        $('.chosen-select').chosen({disable_search_threshold: 3});

    });
</script>
<?php $monitoraggio=0; //smartlook ?>
<footer>
    <p>&copy; LABIMA - 2021</p>
</footer>
