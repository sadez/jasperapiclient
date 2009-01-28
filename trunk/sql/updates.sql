CREATE TABLE reports(
    id      INT(11) NOT NULL, AUTO_INCREMENT PRIMARY KEY,
    name    VARCHAR(60) NOT NULL,
    active  TINYINT(1) NOT NULL DEFAULT 0
)

CREATE TABLE report_prompts(
    id          INT(11) NOT NULL, AUTO_INCREMENT PRIMARY KEY,
    report_id   INT(11) NOT NULL,
    name        VARCHAR(60) NOT NULL,
    type        VARCHAR(50) NOT NULL,
    value       VARCHAR(50) NOT NULL
)

CREATE TABLE report_primpt_properties(
    id                  INT(11) NOT NULL, AUTO_INCREMENT PRIMARY KEY,
    report_prompt_id    INT(110 NOT NULL,
    name                VARCHAR(60) NOT NULL,
    value               VARCHAR(60) NOT NULL
)

CREATE TABLE i_report_prompt_type(
    id      INT(11) NOT NULL, AUTO_INCREMENT PRIMARY KEY,
    type    VARCHAR(30)
)

INSERT INTO i_report_prompt_type (type) VALUES ('Date');
INSERT INTO i_report_prompt_type (type) VALUES ('DateTime');
INSERT INTO i_report_prompt_type (type) VALUES ('DB Select');
INSERT INTO i_report_prompt_type (type) VALUES ('Text');
INSERT INTO i_report_prompt_type (type) VALUES ('Select List');
INSERT INTO i_report_prompt_type (type) VALUES ('Composite');