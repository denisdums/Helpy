(function(wp){
  const { registerPlugin } = wp.plugins || {};
  const { PluginDocumentSettingPanel } = wp.editPost || {};
  const { createElement: h, Fragment } = wp.element || {};
  if (!registerPlugin || !PluginDocumentSettingPanel) return;

  const data = window.HELPY_DATA || { links: [], redmine: {} };

  function LinkItem({label, url, icon, target}) {
    return h('li', { style: { marginBottom: '6px' } },
      h('a', { href: url, target: target || '_blank', rel:'noopener noreferrer' },
        (icon ? icon + ' ' : ''), label
      )
    );
  }

  function RedmineButton({ redmine }) {
    if (!redmine || !redmine.enabled || !redmine.base_url || !redmine.project) return null;
    const path = (redmine.new_issue_path || '/projects/{project}/issues/new').replace('{project}', encodeURIComponent(redmine.project));
    const href = redmine.base_url.replace(/\/+$/,'') + '/' + path.replace(/^\/+/, '');
    return h('p', null,
      h('a', { className: 'components-button is-primary', href: href, target:'_blank', rel:'noopener noreferrer' }, 'Créer un ticket Redmine')
    );
  }

  const Panel = () => {
    const links = Array.isArray(data.links) ? data.links : [];
    return h(PluginDocumentSettingPanel, { name:'helpy-panel', title:'Helpy', className:'helpy-panel' },
      links.length
        ? h(Fragment, null,
            h('ul', { className: 'helpy-list'},
              links.map((l, i) => h(LinkItem, { key:i, ...l }))
            ),
            h(RedmineButton, { redmine: data.redmine })
          )
        : h('p', null, 'Aucun lien configuré pour ce type de contenu.')
    );
  };

  registerPlugin('helpy', { render: Panel, icon: null });
})(window.wp || {});
