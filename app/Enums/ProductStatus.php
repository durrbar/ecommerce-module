<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Enums;

enum ProductStatus: string
{
    case UnderReview = 'under_review';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Publish = 'publish';
    case Unpublish = 'unpublish';
    case Draft = 'draft';
}
