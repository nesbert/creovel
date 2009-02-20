<script language="javascript" type="text/javascript">
<!--
function set_<?=$state_id?>() {
    
    var usaVals = new Array("<?=implode('", "', $us_states)?>");
    var usaIDs = new Array("<?=implode('", "', array_keys($us_states))?>");
    var canadaVals = new Array("<?=implode('", "', $ca_states)?>");
    var canadaIDs = new Array("<?=implode('", "', array_keys($ca_states))?>");
    var countryDrop = document.getElementById("<?=name_to_id($name)?>");
    var selectedCountry = countryDrop.options[countryDrop.selectedIndex].value;
    
    switch ( selectedCountry ) {
        case "United States":
        case "USA":
        case "US":
            update_<?=$state_id?>(usaVals, usaIDs);
        break;
        case "Canada":
        case "CA":
            update_<?=$state_id?>(canadaVals, canadaIDs);
        break;
        default:
            update_<?=$state_id?>();
        break;
    }
}

function update_<?=$state_id?>(stateVals, stateIDs) {
    
    var stateDrop = document.getElementById("<?=$state_id?>");
    stateDrop.options.length = 0;
    stateDrop.options[stateDrop.options.length] = new Option("Please select...", "");
    
    if ( stateVals ) {
        for(var i=0; i<stateVals.length; i++) {
            stateDrop.options[stateDrop.options.length] = new Option(stateVals[i], stateIDs[i]);
            stateDrop.options[0].selected = true;
        }
    } else {
        stateDrop.options.length = 0;
        stateDrop.options[stateDrop.options.length] = new Option("None Available", "");
        stateDrop.options.selected = true;
    }
    
};
-->
</script>
