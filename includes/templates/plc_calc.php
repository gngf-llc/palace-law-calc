<h3>Learn What Your Works Comp Case Could Be Worth</h3>
<p>If you are not sure what your rating is, you can ask your workers compensation assigned physician or call our offices for assistance</p>

<form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
    <strong>Step 1</strong>
    <p>What month and year did your injury occur?</p>
    <div class="timeframe_block">
        <select name="plc_month" id="month_select">
            <?php foreach($params['months'] as $num => $name): ?>
                <option value="<?php echo $num; ?>"><?php echo $name; ?></option>
            <?php endforeach; ?>
        </select>

        <select name="plc_year" class="year_select">
            <?php foreach($params['years'] as $year): ?>
                <option><?php echo $year; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <br />

    <strong>Step 2</strong>
    <p>List your injuries and corresponding rating below:</p>
    <div class="injury_list">
        <p class="injury_block">
            <label class="label_injury" for="injury">Body Part:</label>
            <select name="plc_injuries[]" class="injury_select">
                <option value="" disabled selected>List of Injuries</option>
                <?php foreach($params['injuries'] as $injury): ?>
                    <option><?php echo $injury; ?></option>
                <?php endforeach; ?>
            </select>

            <label class="label_rating" for="rating">Rating:</label>
            <select name="plc_ratings[]" class="rating_select">
                <option value="" disabled selected>Choose a Rating</option>
                <?php foreach($params['ratings'] as $rating): ?>
                    <option><?php echo $rating; ?></option>
                <?php endforeach; ?>
            </select>
            <a href="#" class="delete_injury">x</a>
        </p>
    </div>
    <a href="#" id="add_injury">+ Add a body part</a>
    <br />
    <br />

    <strong>Step 3</strong>
    <p class="buttons">
        <input type='hidden' name='plc_current_step' value='calc' />
        <input id="saveForm" class="button_text" type="submit" name="plc_submit" value="Get My Results" />
    </p>
</form>

