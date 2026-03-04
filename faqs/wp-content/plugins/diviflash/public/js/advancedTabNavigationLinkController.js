
(function($){

    let mutationObserver = new MutationObserver(function(mutations,observer) {
        mutations.forEach(( mutation) => {
            if(mutation.type !== 'childList') return;
            const navigation_link_field = $('input#et-fb-navigation_link');
            if (navigation_link_field.length > 0  && !navigation_link_field.data('processed')){
                navigation_link_field.data('processed', true);
                $(navigation_link_field).prop('readonly', true);
                $(navigation_link_field).attr('style', 'background: rgba(63,13,166,0.1) !important;cursor: copy;');
                $(navigation_link_field).on('click', (event) => {
                    const valueToCopy = event.target.value;

                    const tempTextArea = document.createElement('textarea');
                    tempTextArea.value = valueToCopy;
                    document.body.appendChild(tempTextArea);
                    tempTextArea.select();
                    try {
                        document.execCommand('copy');
                        const notify = document.createElement('span');
                        notify.setAttribute("id", "difl_tab_notify");
                        Object.assign(notify.style, {
                            color: '#a2b0c1',
                            fontSize: '12px',
                            fontWeight: '600',
                            marginTop: '2px',
                            marginLeft: '5px',
                            fontFamily:'inherit'
                        });
                        notify.innerHTML = 'Copied';
                        $(navigation_link_field).parent().append(notify);

                        setTimeout(() => {
                            notify.remove();
                        }, 3000);

                    } catch (err) {
                        console.error('Fallback: Could not copy text:', err);
                    }
                    document.body.removeChild(tempTextArea);
                });
            }else{return;}

        })

    });
    const targetNode = document.getElementById("et-fb-app");
    mutationObserver.observe(targetNode, { childList: true, subtree: true });

})(jQuery)