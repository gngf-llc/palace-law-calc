<div id="plc_container" class="plc_calc_page">
    <h3>Learn What Your Works Comp Case Could Be Worth</h3>
    <p>If you are not sure what your rating is, you can ask your workers compensation assigned physician or call our offices for assistance</p>

    <form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
        <strong>Step 1</strong>
        <p>What month and year did your injury occur?</p>
        <div class="timeframe_block">
            <select name="plc_month" id="month_select" required>
                <?php foreach($params['months'] as $num => $name): ?>
                    <option value="<?php echo $num; ?>"><?php echo $name; ?></option>
                <?php endforeach; ?>
            </select>

            <select name="plc_year" id="year_select" required>
                <?php foreach($params['years'] as $year): ?>
                    <option><?php echo $year; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <br />

        <strong>Step 2</strong>
        <p>List your injuries and corresponding rating below:</p>
        <div class="injury_list">
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

    <?php // template that gets used in injury_list; dont style this directly ?>
    <div id="injury_block_template" style="display: none;">
        <div class='injury_select_block'>
            <label class="label_injury" for="injury">Body Part:</label>
            <select name="plc_injuries[]" class="injury_select" required>
                <option value="" disabled selected>List of Injuries</option>
                <?php foreach($params['injuries'] as $injury_option): ?>
                    <?php echo $injury_option; ?>
                <?php endforeach; ?>
            </select>
        </div>
        <div class='rating_select_block'>
        </div>
        <a href="#" class="delete_injury">x</a>
    </div>

</div>
