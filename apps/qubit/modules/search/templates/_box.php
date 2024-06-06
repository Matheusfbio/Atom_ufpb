<div id="search-form-wrapper" role="search">

  <h2><?php echo __('Search'); ?></h2>

  <form action="<?php echo url_for(['module' => 'informationobject', 'action' => 'browse']); ?>" data-autocomplete="<?php echo url_for(['module' => 'search', 'action' => 'autocomplete']); ?>" autocomplete="off">

    <input type="hidden" name="topLod" value="0"/>
    <input type="hidden" name="sort" value="relevance"/>

    <input type="text" name="query"<?php echo isset($sf_request->query) ? ' class="focused"' : ''; ?> aria-label="<?php echo __('%1%', ['%1%' => sfConfig::get('app_ui_label_globalSearch')]); ?>" value="<?php echo !$sf_user->getAttribute('search-realm') || !sfConfig::get('app_enable_institutional_scoping') ? $sf_request->query : ''; ?>" placeholder="<?php echo __('%1%', ['%1%' => sfConfig::get('app_ui_label_globalSearch')]); ?>"/>

    <button aria-label="<?php echo __('Search'); ?>"></button>

    <div id="search-realm" class="search-popover">

      <?php if (sfConfig::get('app_multi_repository')) { ?>

        <div>
          <label>
              <input name="repos" type="radio" value checked="checked" data-placeholder="<?php echo __('Search'); ?>">
            <?php echo __('Global search'); ?>
          </label>
        </div>

        <?php if (isset($repository)) { ?>
          <div>
            <label>
              <input name="repos" type="radio" value="<?php echo $repository->id; ?>" data-placeholder="<?php echo __('Search %1%', ['%1%' => strip_markdown($repository)]); ?>"/>
              <?php echo __('Search <span>%1%</span>', ['%1%' => render_title($repository)]); ?>
            </label>
          </div>
        <?php } ?>

        <?php if (isset($altRepository)) { ?>
          <div>
            <label>
              <input name="repos" type="radio" value="<?php echo $altRepository->id; ?>" data-placeholder="<?php echo __('Search %1%', ['%1%' => strip_markdown($altRepository)]); ?>"/>
              <?php echo __('Search <span>%1%</span>', ['%1%' => render_title($altRepository)]); ?>
            </label>
          </div>
        <?php } ?>

      <?php } ?>

      <div class="search-realm-advanced">
        <a href="<?php echo url_for(['module' => 'informationobject', 'action' => 'browse', 'showAdvanced' => true, 'topLod' => false]); ?>">
          <?php echo __('Advanced search'); ?>&nbsp;&raquo;
        </a>
      </div>

    </div>

  </form>

</div>
