declare namespace NodeJS {
    export interface ProcessEnv {
        // Common Environment
        NODE_ENV: 'development' | 'test' | 'staging' | 'production';
        TEST_BUILDER: string;
        TEST_HOST: string;

        // LocalWP
        LOCALWP_BEAVER_LINK: string;
        LOCALWP_BEAVER_USERNAME: string;
        LOCALWP_BEAVER_PASSWORD: string;

        LOCALWP_BREAKDANCE_LINK: string;
        LOCALWP_BREAKDANCE_USERNAME: string;
        LOCALWP_BREAKDANCE_PASSWORD: string;

        LOCALWP_BRICKS_LINK: string;
        LOCALWP_BRICKS_USERNAME: string;
        LOCALWP_BRICKS_PASSWORD: string;

        LOCALWP_BRIZY_LINK: string;
        LOCALWP_BRIZY_USERNAME: string;
        LOCALWP_BRIZY_PASSWORD: string;

        LOCALWP_CUSTOM_THEME_LINK: string;
        LOCALWP_CUSTOM_THEME_USERNAME: string;
        LOCALWP_CUSTOM_THEME_PASSWORD: string;

        LOCALWP_DIVI_LINK: string;
        LOCALWP_DIVI_USERNAME: string;
        LOCALWP_DIVI_PASSWORD: string;

        LOCALWP_ELEMENTOR_LINK: string;
        LOCALWP_ELEMENTOR_USERNAME: string;
        LOCALWP_ELEMENTOR_PASSWORD: string;

        LOCALWP_GUTENBERG_LINK: string;
        LOCALWP_GUTENBERG_USERNAME: string;
        LOCALWP_GUTENBERG_PASSWORD: string;

        LOCALWP_NONE_LINK: string;
        LOCALWP_NONE_USERNAME: string;
        LOCALWP_NONE_PASSWORD: string;

        LOCALWP_OXYGEN_CLASSIC_LINK: string;
        LOCALWP_OXYGEN_CLASSIC_USERNAME: string;
        LOCALWP_OXYGEN_CLASSIC_PASSWORD: string;

        LOCALWP_YOOTHEME_LINK: string;
        LOCALWP_YOOTHEME_USERNAME: string;
        LOCALWP_YOOTHEME_PASSWORD: string;

        // Flywheel
        FLYWHEEL_BEAVER_LINK: string;
        FLYWHEEL_BEAVER_USERNAME: string;
        FLYWHEEL_BEAVER_PASSWORD: string;

        FLYWHEEL_BREAKDANCE_LINK: string;
        FLYWHEEL_BREAKDANCE_USERNAME: string;
        FLYWHEEL_BREAKDANCE_PASSWORD: string;

        FLYWHEEL_BRICKS_LINK: string;
        FLYWHEEL_BRICKS_USERNAME: string;
        FLYWHEEL_BRICKS_PASSWORD: string;

        FLYWHEEL_BRIZY_LINK: string;
        FLYWHEEL_BRIZY_USERNAME: string;
        FLYWHEEL_BRIZY_PASSWORD: string;

        FLYWHEEL_CUSTOM_THEME_LINK: string;
        FLYWHEEL_CUSTOM_THEME_USERNAME: string;
        FLYWHEEL_CUSTOM_THEME_PASSWORD: string;

        FLYWHEEL_DIVI_LINK: string;
        FLYWHEEL_DIVI_USERNAME: string;
        FLYWHEEL_DIVI_PASSWORD: string;

        FLYWHEEL_ELEMENTOR_LINK: string;
        FLYWHEEL_ELEMENTOR_USERNAME: string;
        FLYWHEEL_ELEMENTOR_PASSWORD: string;

        FLYWHEEL_GUTENBERG_LINK: string;
        FLYWHEEL_GUTENBERG_USERNAME: string;
        FLYWHEEL_GUTENBERG_PASSWORD: string;

        FLYWHEEL_NONE_LINK: string;
        FLYWHEEL_NONE_USERNAME: string;
        FLYWHEEL_NONE_PASSWORD: string;

        FLYWHEEL_OXYGEN_CLASSIC_LINK: string;
        FLYWHEEL_OXYGEN_CLASSIC_USERNAME: string;
        FLYWHEEL_OXYGEN_CLASSIC_PASSWORD: string;

        FLYWHEEL_YOOTHEME_LINK: string;
        FLYWHEEL_YOOTHEME_USERNAME: string;
        FLYWHEEL_YOOTHEME_PASSWORD: string;

        // WP Engine
        WPENGINE_BEAVER_LINK: string;
        WPENGINE_BEAVER_USERNAME: string;
        WPENGINE_BEAVER_PASSWORD: string;

        WPENGINE_BREAKDANCE_LINK: string;
        WPENGINE_BREAKDANCE_USERNAME: string;
        WPENGINE_BREAKDANCE_PASSWORD: string;

        WPENGINE_BRICKS_LINK: string;
        WPENGINE_BRICKS_USERNAME: string;
        WPENGINE_BRICKS_PASSWORD: string;

        WPENGINE_BRIZY_LINK: string;
        WPENGINE_BRIZY_USERNAME: string;
        WPENGINE_BRIZY_PASSWORD: string;

        WPENGINE_CUSTOM_THEME_LINK: string;
        WPENGINE_CUSTOM_THEME_USERNAME: string;
        WPENGINE_CUSTOM_THEME_PASSWORD: string;

        WPENGINE_DIVI_LINK: string;
        WPENGINE_DIVI_USERNAME: string;
        WPENGINE_DIVI_PASSWORD: string;

        WPENGINE_ELEMENTOR_LINK: string;
        WPENGINE_ELEMENTOR_USERNAME: string;
        WPENGINE_ELEMENTOR_PASSWORD: string;

        WPENGINE_GUTENBERG_LINK: string;
        WPENGINE_GUTENBERG_USERNAME: string;
        WPENGINE_GUTENBERG_PASSWORD: string;

        WPENGINE_NONE_LINK: string;
        WPENGINE_NONE_USERNAME: string;
        WPENGINE_NONE_PASSWORD: string;

        WPENGINE_OXYGEN_CLASSIC_LINK: string;
        WPENGINE_OXYGEN_CLASSIC_USERNAME: string;
        WPENGINE_OXYGEN_CLASSIC_PASSWORD: string;

        WPENGINE_YOOTHEME_LINK: string;
        WPENGINE_YOOTHEME_USERNAME: string;
        WPENGINE_YOOTHEME_PASSWORD: string;
    }
}
