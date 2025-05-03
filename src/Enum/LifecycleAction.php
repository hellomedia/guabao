<?php

namespace App\Enum;

/**
 * Also defined as transition keys in workflow.yaml
 */
enum LifecycleAction: string
{
    const ROUTING_REQUIREMENT = 'publish|unpublish|archive|unarchive|delete';

    case PUBLISH = 'publish';
    case UNPUBLISH = 'unpublish';
    case ARCHIVE = 'archive';
    case UNARCHIVE = 'unarchive';
    case DELETE = 'delete';
    case UNDELETE = 'undelete';
}