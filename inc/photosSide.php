<?php
//create an array of photos to include
$photos_id = array();
array_push($photos_id, '13');
array_push($photos_id, '44');
array_push($photos_id, '19');
array_push($photos_id, '38');
array_push($photos_id, '02');
array_push($photos_id, '26');
array_push($photos_id, '45');
array_push($photos_id, '52');
array_push($photos_id, '55');
array_push($photos_id, '60');
array_push($photos_id, '70');
array_push($photos_id, '58');
array_push($photos_id, '77');
array_push($photos_id, '80');
array_push($photos_id, '01');

?>


<div id="photos" class="four columns offset-by-one">
    <h2>Photos from #SOTB2</h2>
    <div class="sotb2photos">
        <ul>
            <?php
            $random_photos_id = array_rand($photos_id, 4);

            foreach($random_photos_id as $photo_id_key):?>
                <li>
                    <a class="fancybox" href="img/sotb2/DSC_00<?php echo $photos_id[$photo_id_key];?>.jpg">
                        <img src="img/sotb2/DSC_00<?php echo $photos_id[$photo_id_key];?>_small.jpg" alt="State of the Browser badges" />
                    </a>
                </li>
            <?php endforeach;
            ?>
        </ul>
    </div>
</div>