document.addEventListener("DOMContentLoaded", function() {
    let mainContent, elementsToKeep;

    if(display_none_terminator_options.element_id_0){
        mainContent = document.querySelectorAll(display_none_terminator_options.element_id_0);
    } else {
        mainContent = [];
    }

    if(display_none_terminator_options.element_id_exception_1){
        elementsToKeep = document.querySelectorAll(display_none_terminator_options.element_id_exception_1);
    } else {
        elementsToKeep = [];
    }

    function removeElements(element) {
        let children = element.querySelectorAll("*");
        for (let i = 0; i < children.length; i++) {
            if(children[i].tagName === "SCRIPT" || children[i].tagName === "STYLE") {
                continue;
            }
            let computedStyle = window.getComputedStyle(children[i]);
            let shouldKeep = false;
            for(let j = 0; j < elementsToKeep.length; j++){
                if(children[i] === elementsToKeep[j]){
                    shouldKeep = true;
                }
            }
            if (computedStyle.display === "none" || computedStyle.getPropertyValue("display") === "none !important") {
                if(!shouldKeep) {
                    removeElements(children[i]);
                    children[i].remove();
                }
            }
        }
    }

    for (let i = 0; i < mainContent.length; i++) {
        let shouldKeep = false;
        for(let j = 0; j < elementsToKeep.length; j++){
            if(mainContent[i] === elementsToKeep[j]){
                shouldKeep = true;
                break;
            }
        }
        if(!shouldKeep){
            removeElements(mainContent[i]);
            let computedStyle = window.getComputedStyle(mainContent[i]);
            if (computedStyle.display === "none" || computedStyle.getPropertyValue("display") === "none !important") {
                mainContent[i].remove();
            }
        }
    }
});
