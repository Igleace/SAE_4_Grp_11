<div id="index">
<section>
    <h2 class="titre_vertical"> ADIIL</h2>
    <div id="index_carrousel">
        <img src="/assets/photo_accueil_BDE.png" alt="Carrousel ADIIL">
    </div>
</section>

<section>
    <div class="paragraphes">
        <p>
            <b class="underline">L'ADIIL</b>, ou l'<b>Association</b> du <b>Département</b> <b>Informatique</b>
            de l'<b>IUT</b> de <b>Laval</b>,
            est une organisation étudiante dédiée à créer un environnement propice à l'épanouissement dans le
            campus.
            Participer a des évèvements, et plus globalement a la vie du département.
        </p>
        <p>
            L'ADIIL, véritable moteur de la vie étudiante à l'IUT de Laval,
            offre un cadre propice à l'épanouissement académique et social des étudiants en informatique.
            En participant à ses événements variés, les étudiants enrichissent leur expérience universitaire,
            tout en renforçant les liens au sein de la communauté.
        </p>
    </div>
    <h2 class="titre_vertical">L'ASSO</h2>
</section>

<section>
    <h2 class="titre_vertical">SCORES</h2>

    <div id="podium">
        <?php foreach ([2,1,3] as $member_number): ?>
            <?php $pod = $data['podium'][$member_number-1]; ?>
            <div class="podium_unit">
                <h3>#0<?php echo $member_number?></h3>
                <h4><?php echo $pod['prenom_membre'];?></h4>
                <div>
                    <?php if($pod['pp_membre'] == null):?>
                        <img src="/admin/ressources/default_images/user.jpg" alt="Profile Picture"
                        class="profile_picture">
                    <?php else:?>
                        <img src="/api/files/<?php echo $pod['pp_membre'];?>" alt="Profile Picture"
                            class="profile_picture">
                    <?php endif?>
                    <?php echo $pod['xp_membre'];?> xp
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
</div>

<section>
    <div class="events-display">
        <?php foreach ($data['events'] as $event): ?>
            <div class="event" event-id="<?php echo $event['id'];?>">
                <div>
                    <h2><?php echo $event['name'];?></h2>
                    <?php
                        $moisFr = [1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'];

                        $event_date = substr($event['date'], 0, 10);
                        $event_date_info = getdate(strtotime($event_date));
                        echo ucwords($event_date_info["mday"]." ".$moisFr[$event_date_info['mon']].", ".$event["location"]);
                    ?>
                </div>

                <h4 class="event-<?php echo $event['subscription_status']; ?> hover_effect">
                    <?php echo $event['label'];?>
                </h4>
            </div>
        <?php endforeach; ?>
        <h3><a href="/events.php">Voir tous les événements</a></h3>
    </div>
    <h2 class="titre_vertical">EVENT</h2>
</section>