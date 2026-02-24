<?php

require_once 'mainView.php';

class RatingView extends mainView
{
    public static function list($data) {
        $ratings = $data['ratings'] ?? [];
        $score = $data['score'] ?? ['average' => 0, 'count' => 0];
        $productId = $data['product_id'];

        //Falls Durchschnitt null ist (keine Bewertungen), auf 0 setzen
        $avgDisplay = number_format((float)$score['average'], 1);
        $avgVal = (float)($score['average'] ?? 0);
        ?>
        <div class="rating_section" style="margin-top: 40px; border-top: 1px solid #ccc; padding-top: 20px;">

            <div class="rating_summary" style="margin-bottom: 30px;">
                <h2>Kundenbewertungen</h2>
                <div style="font-size: 1.5rem; display: flex; align-items: center; gap: 10px;">
                    <span style="font-weight: bold; font-size: 2rem;"><?= $avgDisplay ?></span>

                    <span style="color: #ffa500;">
                        <?php
                        $fullStars = round($avgVal);

                        for ($i=0; $i<5; $i++) {
                            echo ($i < $fullStars) ? '★' : '☆';
                        }
                        ?>
                    </span>

                    <span style="font-size: 1rem; color: #666;">(<?= $score['count'] ?> Bewertungen)</span>
                </div>
            </div>

            <div class="rating_form_wrapper" style="background: rgba(255,255,255,0.5); padding: 15px; border-radius: 8px; margin-bottom: 30px;">
                <h3>Schreib eine Bewertung</h3>
                <form action="<?= BASE_URL ?>/rating/store" method="POST">
                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($productId) ?>">

                    <label>Deine Bewertung:</label>
                    <select name="rating_stars" required style="padding: 5px;">
                        <option value="5">5 Sterne - Super</option>
                        <option value="4">4 Sterne - Gut</option>
                        <option value="3">3 Sterne - Geht so</option>
                        <option value="2">2 Sterne - Nicht gut</option>
                        <option value="1">1 Stern - Schlecht</option>
                    </select>

                    <br><br>
                    <textarea
                    name="rating_comment"
                    rows="3"
                    placeholder="Was hat dir gefallen?"
                    style="width:100%; padding:5px; resize:none; overflow:hidden;"
                    oninput="this.style.height='auto'; this.style.height=this.scrollHeight+'px';"
                    ></textarea>                    
                    <br><br>
                    <button type="submit" style="padding: 8px 16px; background: #333; color: #fff; border: none; cursor: pointer;">Senden</button>
                </form>
            </div>

            <div class="rating_list">
                <?php if (empty($ratings)): ?>
                    <p>Noch keine Bewertungen. Sei der Erste!</p>
                <?php else: ?>
                    <?php foreach ($ratings as $r): ?>
                        <div class="single_rating" style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">

                            <div style="display: flex; justify-content: space-between;">
                                <strong><?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?></strong>
                                <span style="color: #888; font-size: 0.9em;"><?= $r['date'] ?></span>
                            </div>

                            <div style="color: #ffa500;">
                                <?php
                                for ($i = 0; $i < 5; $i++) {
                                    echo ($i < $r['rating_score']) ? '★' : '☆';
                                }
                                ?>
                            </div>

                            <p style="margin-top: 5px;"><?= nl2br(htmlspecialchars($r['comment'])) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }


}