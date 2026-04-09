<h1>LES EVENEMENTS</h1>

<section>
    <a class="show-more" href="/?page=events&show=<?= $data['show'] + 10 ?>">
        Voir plus loin dans le passé
    </a>

    <div class="events-display">
        <?php
        $joursFr = [0 => 'Dimanche', 1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi'];
        $moisFr = [1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'];
        ?>

        <?php if (!empty($data['events'])): ?>
            <?php
            $currentDate = new DateTime(date('Y-m-d'));
            $markNext = true;
            ?>
            <?php foreach ($data['events'] as $event): ?>
                <?php
                $eventDate = substr($event['date'], 0, 10);
                $eventDateObj = new DateTime($eventDate);
                $eventDateInfo = getdate(strtotime($eventDate));

                $isPassed = false;
                $closestClass = '';

                if ($eventDateObj < $currentDate) {
                    $datePinClass = 'passed';
                    $datePinLabel = 'Passé';
                    $otherClasses = 'passed';
                    $isPassed = true;
                } elseif ($eventDateObj == $currentDate) {
                    $datePinClass = 'today';
                    $datePinLabel = "Aujourd'hui";
                    $closestClass = 'closest-event';
                    $otherClasses = '';
                    $markNext = false;
                } else {
                    $datePinClass = 'upcoming';
                    $datePinLabel = 'À venir';
                    $otherClasses = '';
                    if ($markNext) {
                        $closestClass = 'closest-event';
                        $markNext = false;
                    }
                }
                ?>
                <div class="event-box <?= $otherClasses ?>" id="<?= $closestClass ?>">
                    <div class="timeline-event">
                        <h4>
                            <?= ucwords($joursFr[$eventDateInfo['wday']] . ' ' . $eventDateInfo['mday'] . ' ' . $moisFr[$eventDateInfo['mon']]); ?>
                        </h4>
                        <div class="vertical-line"></div>
                        <p><?= $datePinLabel; ?></p>
                        <div class="timeline-marker <?= $datePinClass ?>">
                            <div class="time-line"></div>
                        </div>
                    </div>

                    <div class="event" event-id="<?= htmlspecialchars($event['id']) ?>">
                        <div>
                            <h2><?= htmlspecialchars($event['name']) ?></h2>
                            <?= ucwords(htmlspecialchars($event['location'])) ?>
                        </div>

                        <h4 class="event-<?= htmlspecialchars($event['subscription_status']) ?> hover_effect">
                            <?= htmlspecialchars($event['label']) ?>
                        </h4>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun événement trouvé.</p>
        <?php endif; ?>
    </div>
</section>

<script src="/scripts/event_details_redirect.js"></script>
<script src="/scripts/scroll_to_closest_event.js"></script>