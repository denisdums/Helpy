(function(wp){
  const { registerPlugin } = wp.plugins || {};
  const { PluginDocumentSettingPanel } = wp.editPost || {};
  const { createElement: h, Fragment } = wp.element || {};
  if (!registerPlugin || !PluginDocumentSettingPanel) return;

  const data = window.HELPY_DATA || { links: [], ticketing: {}, ctx: {} };

  function LinkItem({label, url, icon, target}) {
    return h('li', { className: 'helpy-li' },
      h('a', { href: url, target: target || '_blank', rel:'noopener noreferrer' },
        (icon ? icon + ' ' : ''), label
      )
    );
  }

  function TicketButton({ ticketing, ctx }) {
    if (!ticketing || !ticketing.enabled || !ticketing.base_url) return null;
    const repl = (s, map) => s.replace(/{project}|{title}|{postId}|{postType}/g, (m)=> {
      const k = m.slice(1,-1);
      return encodeURIComponent(map[k] || '');
    });

    const path = repl(ticketing.new_issue_path || '/', {
      project: ticketing.project || '',
      title: ctx?.title || '',
      postId: ctx?.postId || '',
      postType: ctx?.postType || ''
    });

    const href = ticketing.base_url.replace(/\/+$/,'') + '/' + String(path).replace(/^\/+/,'');
    const label = ticketing.button_label || 'Create ticket';

    return h('p', null,
      h('a', { className: 'components-button is-primary', href: href, target:'_blank', rel:'noopener noreferrer' },
        label
      )
    );
  }

  const Panel = () => {
    const links = Array.isArray(data.links) ? data.links : [];
    return h(PluginDocumentSettingPanel, { name:'helpy-panel', title:'Helpy', className:'helpy-panel' },
      links.length
        ? h(Fragment, null,
            h('ul', { className: 'helpy-list' },
              links.map((l, i) => h(LinkItem, { key:i, ...l }))
            ),
            h(TicketButton, { ticketing: data.ticketing, ctx: data.ctx })
          )
        : h('p', null, 'No links configured for this content type.')
    );
  };

  registerPlugin('helpy', { render: Panel, icon: null });
})(window.wp || {});
