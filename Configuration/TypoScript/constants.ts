plugin.tx_rkwsurvey_survey {
	view {
		# cat=plugin.tx_rkwsurvey_survey/file; type=string; label=Path to template root (FE)
		templateRootPath = EXT:rkw_survey/Resources/Private/Templates/
		# cat=plugin.tx_rkwsurvey_survey/file; type=string; label=Path to template partials (FE)
		partialRootPath = EXT:rkw_survey/Resources/Private/Partials/
		# cat=plugin.tx_rkwsurvey_survey/file; type=string; label=Path to template layouts (FE)
		layoutRootPath = EXT:rkw_survey/Resources/Private/Layouts/
	}
	persistence {
		# cat=plugin.tx_rkwsurvey_survey//a; type=string; label=Default storage PID
		storagePid =
	}

    settings {

        # cat=plugin.tx_rkwsurvey_survey/file; type=string; label=Show survey name on intro and extro
        showSurveyNameProgessIntroExtro = 0

        # cat=plugin.tx_rkwsurvey_survey/file; type=string; label=Show survey name on progress
        showSurveyNameProgess = 0
    }
}

module.tx_rkwsurvey_evaluation {
	view {

        # cat=module.tx_rkwsurvey_evaluation/file; type=string; label=Path to template root (BE)
		templateRootPath = EXT:rkw_survey/Resources/Private/Backend/Templates/
		# cat=module.tx_rkwsurvey_evaluation/file; type=string; label=Path to template partials (BE)
		partialRootPath = EXT:rkw_survey/Resources/Private/Backend/Partials/
		# cat=module.tx_rkwsurvey_evaluation/file; type=string; label=Path to template layouts (BE)
		layoutRootPath = EXT:rkw_survey/Resources/Private/Backend/Layouts/
    }

}
