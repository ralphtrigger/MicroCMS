insert into t_article values
(1, 'First article', 'Hi there! This is the very first article.');

insert into t_article values
(2, 'Lorem ipsum', 'In non elit dapibus, dignissim nisi eget, convallis odio.
  Donec consectetur risus sed dapibus volutpat. Sed nec tincidunt est. Maecenas
  facilisis magna vitae condimentum dictum. Fusce augue lectus, aliquet ut odio
  at, placerat laoreet enim. Proin cursus mauris varius nunc porta feugiat.
  Pellentesque ac eleifend velit. Cras laoreet placerat euismod.
  Praesent vitae orci consequat, ultricies risus a, consequat ligula.
  Curabitur posuere interdum tincidunt. Quisque feugiat, magna et volutpat
  cursus, orci quam laoreet tellus, ut venenatis eros ex in nibh. Vivamus
  tristique libero eget ipsum aliquam, a auctor orci laoreet. Quisque mollis,
  lectus vel scelerisque lacinia, odio risus bibendum mi, et vehicula eros ante
  id orci. Proin elementum, purus id semper varius, magna quam gravida augue,
  quis bibendum lacus mauris et velit. Nullam tortor ex, porttitor non arcu at,
  gravida faucibus erat. Quisque dignissim dolor eget erat viverra, at mattis
  urna interdum.');

insert into t_article values
(3, "Lorem ipsum in french", "J’en dis autant de ceux qui, par mollesse d’esprit,
  c’est-à-dire par la crainte de la peine et de la douleur,
  manquent aux devoirs de la vie. Et il est très facile de rendre raison de ce
  que j’avance. Car, lorsque nous sommes tout à fait libres, et que rien ne nous
  empêche de faire ce qui peut nous donner le plus de plaisir, nous pouvons nous
  livrer entièrement à la volupté et chasser toute sorte de douleur ; mais, dans
  les temps destinés aux devoirs de la société ou à la nécessité des affaires,
  souvent il faut faire divorce avec la volupté, et ne se point refuser à la peine.
  La règle que suit en cela un homme sage, c’est de renoncer à de légères voluptés
  pour en avoir de plus grandes, et de savoir supporter des douleurs légères pour
  en éviter de plus fâcheuses.");

/* raw password is 'john' */
insert into t_user values
(1, 'JohnDoe', 
'L2nNR5hIcinaJkKR+j4baYaZjcHS0c3WX2gjYF6Tmgl1Bs+C9Qbr+69X8eQwXDvw0vp73PrcSeT0bGEW5+T2hA==', 
'YcM=A$nsYzkyeDVjEUa7W9K', 'ROLE_USER');

/* raw password is 'jane' */
insert into t_user values
(2, 'JaneDoe', 
'EfakNLxyhHy2hVJlxDmVNl1pmgjUZl99gtQ+V3mxSeD8IjeZJ8abnFIpw9QNahwAlEaXBiQUBLXKWRzOmSr8HQ==', 
'dhMTBkzwDKxnD;4KNs,4ENy', 'ROLE_USER');

/* raw password is '@dm1n' */
insert into t_user values
(3, 'admin', 
'gqeuP4YJ8hU3ZqGwGikB6+rcZBqefVy+7hTLQkOD+jwVkp4fkS7/gr1rAQfn9VUKWc7bvOD7OsXrQQN5KGHbfg==', 
'EDDsl&fBCJB|a5XUtAlnQN8', 'ROLE_ADMIN');

insert into t_comment values
(1, 'Great! keep up the good work.', 1, 1);

insert into t_comment values
(2, "Thank you, I'll try my best.", 1, 2);
