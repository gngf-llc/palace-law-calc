<div id="plc_container" class="plc_calc_page">
    <h3>Learn What Your Workers' Comp Case Could Be Worth</h3>
    <p>In just 3 easy steps, you can figure out how much you can receive from your worker's compensation case.</p>

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
        <p class="buttons step_3_toggle">
            <input id="saveForm" class="button_text" type="submit" name="plc_submit" value="Get My Results" />
        </p>

        <div id="step_3">
            <p>
                <label class="description" for="plc_name">Name *</label>
                <input id="plc_last_name" name="plc_name" class="" type="text" maxlength="255" value="<?php echo (isset($_POST['plc_name']) ? esc_attr($_POST['plc_name']) : '' ); ?>" required='required' />
            </p>
            <p>
                <label class="description" for="plc_email">Email *</label>
                <input id="plc_email" name="plc_email" class="" type="email" maxlength="255" placeholder="user@example.com" value="<?php echo (isset($_POST['plc_email']) ? esc_attr($_POST['plc_email']) : '' ); ?>" required='required' />
            </p>
            <p class="buttons">
                <input type='hidden' name='plc_current_step' value='calc' />
                <input id="saveForm" class="button_text" type="submit" name="plc_submit" value="Show My Results" />
            </p>
        </div>

    </form>

    <?php // template that gets used in injury_list; dont style this directly ?>
    <div id="injury_block_template" style="display: none;">
        <div class='injury_select_block'>
            <label class="label_injury" for="injury"></label>
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
