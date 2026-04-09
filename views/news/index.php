<h1>ACTUALITES</h1>

<section>
    <a class="show-more" href="/?page=news&show=<?= $data['show'] + 10 ?>">
        Voir plus loin dans le passé
    </a>

    <div class="events-display">
        <?php
        $joursFr = [0 => 'Dimanche', 1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi'];
        $moisFr = [1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'];

        $current_date = new DateTime(date('Y-m-d'));
        $closest_event_id = '';
        ?>

        <?php if (!empty($data['news'])): ?>
            <?php foreach ($data['news'] as $event): ?>
                <?php
                $eventid = $event['id_actualite'];
                $event_date = substr($event['date_actualite'], 0, 10);
                $event_date_info = getdate(strtotime($event_date));
                $event_date_obj = new DateTime($event_date);

                if ($event_date_obj == $current_date) {
                    $closest_event_id = 'closest-event';
                } elseif (empty($closest_event_id)) {
                    $closest_event_id = 'closest-event';
                }
                ?>
                <div class="event-box" id="<?= $closest_event_id ?>">
                    <div class="timeline-event">
                        <h4>
                            <?= ucwords($joursFr[$event_date_info['wday']] . ' ' . $event_date_info['mday'] . ' ' . $moisFr[$event_date_info['mon']]); ?>
                        </h4>
                        <div class="vertical-line"></div>
                    </div>
                    <div class="event" event-id="<?= htmlspecialchars($eventid) ?>">
                        <div>
                            <h2 style="margin-bottom: 0;">
                                <?= htmlspecialchars($event['titre_actualite']) ?>
                            </h2>
                        </div>
                        <h4 class="event-not-subscribed">
                            Consulter
                        </h4>
                    </div>
                </div>
                <?php $closest_event_id = ''; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucune actualité trouvée.</p>
        <?php endif; ?>
    </div>
</section>