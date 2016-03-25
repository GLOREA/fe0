<html>
    <head>
        <title></title>
        <style>
            .card {
                margin: 0 auto 3em auto;
            }
        </style>
    </head>
    <body>
<?php
require_once dirname(__FILE__) . '/lib/database.php';

$query = <<<"EOT"
SELECT
  s.id,
  s.name,
  s.cost,
  s.effect,
  s.level,
  st.name_en AS type_name,
  st.icon_url_id AS type_icon_url_id,
  group_concat(sst.name_en) AS sub_type_names,
  group_concat(sst.icon_url_id) AS sub_type_icon_url_ids
  FROM
    skills AS s
  LEFT OUTER JOIN
    skill_types AS st ON s.skill_type_id = st.id
  LEFT OUTER JOIN
    skill_skill_sub_types AS ssst ON s.id = ssst.skill_id
  LEFT OUTER JOIN
    skill_sub_types AS sst ON ssst.skill_sub_type_id = sst.id
  GROUP BY s.id;
EOT;

$results = Database::exec(
    $query
);

$skills = Array();
foreach($results as $skill){
    $skills[(int)$skill['id']] = $skill;
}

$query = <<<"EOT"
SELECT
  c.id,
  c.name,
  c.name_kana,
  c.flavor_text,
  u.name AS unit_name,
  sym.name AS symbol,
  exp.name AS expansion,
  rar.name AS rarity,
  rar.abbreviation AS rarity_symbol,
  CONCAT(exp_t.charactor, LPAD(exp.number, 2, '0'), '-',LPAD(c.card_no, 3, '0')) AS card_no,
  ill.name AS illustrator,
  c.attack_cost,
  c.classchange_cost,
  c.attack,
  c.status,
  sexes.name AS sex,
  uc.name AS unit_class,
  w.name AS warrior,
  group_concat(cs.skill_id) AS skill_ids
  FROM
    cards AS c
  LEFT OUTER JOIN
    symbols AS sym ON c.symbol_id = sym.id
  LEFT OUTER JOIN
    expansions AS exp ON c.expansion_id = exp.id
  LEFT OUTER JOIN
    expansion_types AS exp_t ON exp.expansion_type_id = exp_t.id
  LEFT OUTER JOIN
    rarities AS rar ON c.rarity_id = rar.id
  LEFT OUTER JOIN
    illustrators AS ill ON c.illustrator_id = ill.id
  LEFT OUTER JOIN
    unit_classes AS uc ON c.unit_class_id = uc.id
  LEFT OUTER JOIN
    warriors AS w ON c.warrior_id = w.id
  LEFT OUTER JOIN
    units AS u ON c.unit_id = u.id
  LEFT OUTER JOIN
    sexes ON c.sex_id = sexes.id
  LEFT OUTER JOIN
    card_skills AS cs ON c.id = cs.card_id
  GROUP BY c.id;
EOT;

$results = Database::exec(
    $query
);
?>

<?php
foreach($results AS $card){
?>
<div class="card">
    <b><a href=""><?php print $card['name'];?></a></b>
    　<?php print $card['symbol'];?>　<?php print $card['attack_cost'] . (empty($card['classchange_cost']) ? '' : (' / ' . $card['classchange_cost']));?>
    <div><?php print $card['unit_name'];?> ― <?php print $card['unit_class'];?>・<?php print $card['warrior'];?>　<?php print $card['rarity_symbol'];?>, <?php print $card['expansion'];?></div>
<?php
foreach(explode(',', $card['skill_ids']) as $skill_id){
    $skill = $skills[(int)$skill_id];
    print '<p>';
    print '<b>' . $skill['name'] . '</b>　';
    print $skill['sub_type_names'];
    if(!empty($skill['cost'])){ print '【' . $skill['cost'] . '】'; }
    print $skill['type_names'];
    print $skill['effect'];
    print '</p>';
}
?>
    <div><?php print $card['attack'] . ' / ' . $card['status'];?></div>
    <div>Illus.<?php print $card['illustrator'];?> (<?php print $card['card_no'];?>)</div>
</div>
<?php
}
?>
