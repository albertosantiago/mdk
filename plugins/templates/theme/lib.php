<?php

/**
 * Makes our changes to the CSS
 *
 * @param string $css
 * @param theme_config $theme
 * @return string 
 */
function ##__PLUGIN_NAME__##_process_css($css, $theme) {

    // Set the link color
    if (!empty($theme->settings->linkcolor)) {
        $linkcolor = $theme->settings->linkcolor;
    } else {
        $linkcolor = null;
    }
    $css = ##__PLUGIN_NAME__##_set_linkcolor($css, $linkcolor);

	// Set the link hover color
    if (!empty($theme->settings->linkhover)) {
        $linkhover = $theme->settings->linkhover;
    } else {
        $linkhover = null;
    }
    $css = ##__PLUGIN_NAME__##_set_linkhover($css, $linkhover);
    
    
    // Set the background color
    if (!empty($theme->settings->backgroundcolor)) {
        $backgroundcolor = $theme->settings->backgroundcolor;
    } else {
        $backgroundcolor = null;
    }
    $css = ##__PLUGIN_NAME__##_set_backgroundcolor($css, $backgroundcolor);
    
    
    

    // Return the CSS
    return $css;
}

/**
 * Sets the link color variable in CSS
 *
 */
function ##__PLUGIN_NAME__##_set_linkcolor($css, $linkcolor) {
    $tag = '[[setting:linkcolor]]';
    $replacement = $linkcolor;
    if (is_null($replacement)) {
        $replacement = '#2a65b1';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function ##__PLUGIN_NAME__##_set_linkhover($css, $linkhover) {
    $tag = '[[setting:linkhover]]';
    $replacement = $linkhover;
    if (is_null($replacement)) {
        $replacement = '#222222';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}


function ##__PLUGIN_NAME__##_set_backgroundcolor($css, $backgroundcolor) {
    $tag = '[[setting:backgroundcolor]]';
    $replacement = $backgroundcolor;
    if (is_null($replacement)) {
        $replacement = '#454545';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

