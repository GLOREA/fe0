<?php
/*
DB構造メモ書き
[Arms] 武器
-- id
-- name
-- icon_url

[Expansions] エキスパンション
-- id
-- name
-- expansion_type_id
-- number

[ExpansionTypes] エキスパンションタイプ
-- id
-- name
-- charactor

[CardArms]
-- card_id
-- arm_id

[Cards] カード
-- id
-- name
-- name_kana
-- flavor_text
-- unit_id
-- symbol_id
-- rarity_id
-- expansion_id
-- card_no
-- illustrator_id
-- attack_cost
-- classchange_cost
-- sex_id
-- class_id
-- warrior_id

[CardRanges] 射程
-- card_id
-- range

[CardSkills]
-- card_id
-- skill_id

[CardTypes]
-- card_id
-- type_id

[Classes] クラス
-- id
-- name

[Illustrators] イラスト
-- id
-- name

[Sexes] 性別
-- id
-- string
-- icon_url

[Skills] スキル
-- id
-- skill_type_id
-- cost (そのままのテキストを埋め込もうとは思うが…うーん…）
-- name
-- effect
-- level （0が基本。サブタイプにレベルアップがあって初めて意味を成す）

[SkillSkillSubTypes]
-- skill_id
-- skill_sub_type_id

[SkillTypes] スキルタイプ（起動・自動・常時・特殊）
-- id

[SkillSubTypes] スキルサブタイプ（クラスチェンジスキル・攻撃の紋章・防御の紋章・レベルアップスキル）
-- id

[Symbols]
-- id
-- name
-- icon_url

[Rarities] レアリティ
-- id
-- name
-- is_foil （プラスかどうかで判断）

[Types] タイプ
-- id
-- name
-- icon_url

[Warriors] 兵種
-- id
-- name

[Units] ユニット
-- id
-- name
*/


