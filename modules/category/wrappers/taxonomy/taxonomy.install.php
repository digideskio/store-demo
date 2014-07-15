<?php
// $Id: taxonomy.install.php,v 1.1 2009/03/05 22:23:56 jaza Exp $

/**
 * Implementation of hook_schema().
 */
function taxonomy_schema() {
  $schema['term_data'] = array(
    'description' => t('Stores term information.'),
    'fields' => array(
      'tid' => array(
        'type' => 'int',
        'unsigned' => TRUE,
        'not null' => TRUE,
        'description' => t('Primary Key: Unique term ID.'),
      ),
      'vid' => array(
        'type' => 'int',
        'unsigned' => TRUE,
        'not null' => TRUE,
        'default' => 0,
        'description' => t('The {vocabulary}.vid of the vocabulary to which the term is assigned.'),
      ),
      'name' => array(
        'type' => 'varchar',
        'length' => 255,
        'not null' => TRUE,
        'default' => '',
        'description' => t('The term name.'),
      ),
      'description' => array(
        'type' => 'text',
        'not null' => FALSE,
        'size' => 'big',
        'description' => t('A description of the term.'),
      ),
      'weight' => array(
        'type' => 'int',
        'not null' => TRUE,
        'default' => 0,
        'size' => 'tiny',
        'description' => t('The weight of this term in relation to other terms.'),
      ),
    ),
    'primary key' => array('tid'),
    'indexes' => array(
      'taxonomy_tree' => array('vid', 'weight', 'name'),
      'vid_name' => array('vid', 'name'),
    ),
  );

  $schema['term_hierarchy'] = array(
    'description' => t('Stores the hierarchical relationship between terms.'),
    'fields' => array(
      'tid' => array(
        'type' => 'int',
        'unsigned' => TRUE,
        'not null' => TRUE,
        'default' => 0,
        'description' => t('Primary Key: The {term_data}.tid of the term.'),
      ),
      'parent' => array(
        'type' => 'int',
        'unsigned' => TRUE,
        'not null' => TRUE,
        'default' => 0,
        'description' => t("Primary Key: The {term_data}.tid of the term's parent. 0 indicates no parent."),
      ),
    ),
    'indexes' => array(
      'parent' => array('parent'),
    ),
    'primary key' => array('tid', 'parent'),
  );

  $schema['term_node'] = array(
    'description' => t('Stores the relationship of terms to nodes.'),
    'fields' => array(
      'nid' => array(
        'type' => 'int',
        'unsigned' => TRUE,
        'not null' => TRUE,
        'default' => 0,
        'description' => t('Primary Key: The {node}.nid of the node.'),
      ),
      'vid' => array(
        'type' => 'int',
        'unsigned' => TRUE,
        'not null' => TRUE,
        'default' => 0,
        'description' => t('Primary Key: The {node}.vid of the node.'),
      ),
      'tid' => array(
        'type' => 'int',
        'unsigned' => TRUE,
        'not null' => TRUE,
        'default' => 0,
        'description' => t('Primary Key: The {term_data}.tid of a term assigned to the node.'),
      ),
    ),
    'indexes' => array(
      'vid' => array('vid'),
      'nid' => array('nid'),
    ),
    'primary key' => array('tid', 'vid'),
  );

  $schema['term_relation'] = array(
    'description' => t('Stores non-hierarchical relationships between terms.'),
    'fields' => array(
      'trid' => array(
        'type' => 'serial',
        'not null' => TRUE,
        'description' => t('Primary Key: Unique term relation ID.'),
      ),
      'tid1' => array(
        'type' => 'int',
        'unsigned' => TRUE,
        'not null' => TRUE,
        'default' => 0,
        'description' => t('The {term_data}.tid of the first term in a relationship.'),
      ),
      'tid2' => array(
        'type' => 'int',
        'unsigned' => TRUE,
        'not null' => TRUE,
        'default' => 0,
        'description' => t('The {term_data}.tid of the second term in a relationship.'),
      ),
    ),
    'unique keys' => array(
      'tid1_tid2' => array('tid1', 'tid2'),
    ),
    'indexes' => array(
      'tid2' => array('tid2'),
    ),
    'primary key' => array('trid'),
  );

  $schema['term_synonym'] = array(
    'description' => t('Stores term synonyms.'),
    'fields' => array(
      'tsid' => array(
        'type' => 'serial',
        'not null' => TRUE,
        'description' => t('Primary Key: Unique term synonym ID.'),
      ),
      'tid' => array(
        'type' => 'int',
        'unsigned' => TRUE,
        'not null' => TRUE,
        'default' => 0,
        'description' => t('The {term_data}.tid of the term.'),
      ),
      'name' => array(
        'type' => 'varchar',
        'length' => 255,
        'not null' => TRUE,
        'default' => '',
        'description' => t('The name of the synonym.'),
      ),
    ),
    'indexes' => array(
      'tid' => array('tid'),
      'name_tid' => array('name', 'tid'),
    ),
    'primary key' => array('tsid'),
  );

  $schema['vocabulary'] = array(
    'description' => t('Stores vocabulary information.'),
    'fields' => array(
      'vid' => array(
        'type' => 'int',
        'unsigned' => TRUE,
        'not null' => TRUE,
        'description' => t('Primary Key: Unique vocabulary ID.'),
      ),
      'name' => array(
        'type' => 'varchar',
        'length' => 255,
        'not null' => TRUE,
        'default' => '',
        'description' => t('Name of the vocabulary.'),
      ),
      'description' => array(
        'type' => 'text',
        'not null' => FALSE,
        'size' => 'big',
        'description' => t('Description of the vocabulary.'),
      ),
      'help' => array(
        'type' => 'varchar',
        'length' => 255,
        'not null' => TRUE,
        'default' => '',
        'description' => t('Help text to display for the vocabulary.'),
      ),
      'relations' => array(
        'type' => 'int',
        'unsigned' => TRUE,
        'not null' => TRUE,
        'default' => 0,
        'size' => 'tiny',
        'description' => t('Whether or not related terms are enabled within the vocabulary. (0 = disabled, 1 = enabled)'),
      ),
      'hierarchy' => array(
        'type' => 'int',
        'unsigned' => TRUE,
        'not null' => TRUE,
        'default' => 0,
        'size' => 'tiny',
        'description' => t('The type of hierarchy allowed within the vocabulary. (0 = disabled, 1 = single, 2 = multiple)'),
      ),
      'multiple' => array(
        'type' => 'int',
        'unsigned' => TRUE,
        'not null' => TRUE,
        'default' => 0,
        'size' => 'tiny',
        'description' => t('Whether or not multiple terms from this vocabulary may be assigned to a node. (0 = disabled, 1 = enabled)'),
      ),
      'required' => array(
        'type' => 'int',
        'unsigned' => TRUE,
        'not null' => TRUE,
        'default' => 0,
        'size' => 'tiny',
        'description' => t('Whether or not terms are required for nodes using this vocabulary. (0 = disabled, 1 = enabled)'),
      ),
      'tags' => array(
        'type' => 'int',
        'unsigned' => TRUE,
        'not null' => TRUE,
        'default' => 0,
        'size' => 'tiny',
        'description' => t('Whether or not free tagging is enabled for the vocabulary. (0 = disabled, 1 = enabled)'),
      ),
      'module' => array(
        'type' => 'varchar',
        'length' => 255,
        'not null' => TRUE,
        'default' => '',
        'description' => t('The module which created the vocabulary.'),
      ),
      'weight' => array(
        'type' => 'int',
        'not null' => TRUE,
        'default' => 0,
        'size' => 'tiny',
        'description' => t('The weight of the vocabulary in relation to other vocabularies.'),
      ),
    ),
    'primary key' => array('vid'),
    'indexes' => array(
      'list' => array('weight', 'name'),
    ),
  );

  $schema['vocabulary_node_types'] = array(
    'description' => t('Stores which node types vocabularies may be used with.'),
    'fields' => array(
      'vid' => array(
        'type' => 'int',
        'unsigned' => TRUE,
        'not null' => TRUE,
        'default' => 0,
        'description' => t('Primary Key: the {vocabulary}.vid of the vocabulary.'),
      ),
      'type' => array(
        'type' => 'varchar',
        'length' => 32,
        'not null' => TRUE,
        'default' => '',
        'description' => t('The {node}.type of the node type for which the vocabulary may be used.'),
      ),
    ),
    'primary key' => array('type', 'vid'),
    'indexes' => array(
      'vid' => array('vid'),
    ),
  );

  return $schema;
}

